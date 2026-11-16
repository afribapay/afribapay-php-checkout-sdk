<?php
/**
 * Verifies the AfribaPay notification sent to notify_url (AFRIBAPAY_SIGN = HMAC-SHA256 of the raw body, keyed with apiKey).
 * nginx (underscores_in_headers off) and Apache 2.4 drop headers containing "_": enable them, or every notification gets a 403.
 *
 *   try {
 *       $payment = AfribaPayNotification::verify();
 *   } catch (AfribaPayNotificationException $e) {
 *       AfribaPayNotification::acknowledge($e->getHttpCode(), $e->getMessage());
 *       exit;
 *   }
 *   if ($payment->isSuccessful() && $payment->matchesOrder($orderId, $amount, $currency, $country)) { ... }
 *   AfribaPayNotification::acknowledge();
 */

if (!class_exists('AfribaPayException')) {
    class AfribaPayException extends Exception {}
}

if (!class_exists('AfribaPayNotificationException')) {
    class AfribaPayNotificationException extends AfribaPayException {
        private int $httpCode;

        public function __construct(string $message, int $httpCode = 400) {
            parent::__construct($message);
            $this->httpCode = $httpCode;
        }

        public function getHttpCode(): int {
            return $this->httpCode;
        }
    }
}

if (!class_exists('AfribaPayPayment')) {
    final class AfribaPayPayment {
        public const SUCCESS = 'SUCCESS';
        public const FAILED  = 'FAILED';
        public const PENDING = 'PENDING';

        public string $status;
        public ?string $transactionId;
        public ?string $orderId;
        public ?string $referenceId;

        public ?float $amount;
        public ?string $currency;
        public ?string $operator;
        public ?string $phoneNumber;
        public ?string $country;
        public array $raw;

        public function __construct(array $data) {
            $this->raw           = $data;
            $this->status        = (string)($data['status'] ?? '');
            $this->transactionId = self::str($data['transaction_id'] ?? null);
            $this->orderId       = self::str($data['order_id'] ?? null);
            $this->referenceId   = self::str($data['reference_id'] ?? null);
            // original_amount is the amount requested; amount/net_amount depend on the fee configuration
            $requested           = $data['original_amount'] ?? null;
            $this->amount        = is_numeric($requested) ? (float)$requested : null;
            $this->currency      = isset($data['currency']) ? strtoupper((string)$data['currency']) : null;
            $this->operator      = self::str($data['operator'] ?? null);
            $this->phoneNumber   = self::str($data['phone_number'] ?? null);
            $this->country       = self::str($data['country'] ?? null);
        }

        public function isSuccessful(): bool { return $this->status === self::SUCCESS; }
        public function isFailed(): bool     { return $this->status === self::FAILED; }
        // PENDING is not final: never confirm or cancel an order on it
        public function isFinal(): bool      { return $this->isSuccessful() || $this->isFailed(); }

        public function matchesOrder(string $orderId, $amount, string $currency, ?string $country = null): bool {
            return $this->orderId !== null
                && ($country === null || ($this->country !== null && strcasecmp($country, $this->country) === 0))
                && hash_equals($orderId, $this->orderId)
                && $this->amount !== null
                && is_numeric($amount)
                && abs($this->amount - (float)$amount) < 0.000001
                && $this->currency === strtoupper($currency);
        }

        private static function str($value): ?string {
            return $value === null || $value === '' ? null : (string)$value;
        }
    }
}

if (!class_exists('AfribaPayNotification')) {
    final class AfribaPayNotification {
        /**
         * @param bool          $confirmWithApi Re-fetch the transaction from the Status API
         * @param string|null   $rawBody        Default: php://input
         * @param string|null   $signature      Default: AFRIBAPAY_SIGN request header
         * @param string|null   $configPath     Default: customer_config.inc.php next to this file
         * @param callable|null $http           Tests only: fn(string $method, string $url, array $headers): array{0:int,1:string}
         * @throws AfribaPayNotificationException
         */
        public static function verify(bool $confirmWithApi = false, ?string $rawBody = null, ?string $signature = null, ?string $configPath = null, ?callable $http = null): AfribaPayPayment {
            if ($rawBody === null && ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                throw new AfribaPayNotificationException('Method Not Allowed', 405);
            }
            $rawBody   = $rawBody ?? (string)file_get_contents('php://input');
            $signature = $signature ?? self::signatureHeader();
            $config    = self::loadConfig($configPath ?? __DIR__ . '/customer_config.inc.php');

            if ($rawBody === '') {
                throw new AfribaPayNotificationException('Empty webhook payload.', 400);
            }

            $expected = hash_hmac('sha256', $rawBody, (string)$config['apiKey']);
            if ($signature === null || $signature === '' || !hash_equals($expected, strtolower(trim($signature)))) {
                throw new AfribaPayNotificationException('Invalid webhook signature.', 403);
            }

            $data = json_decode($rawBody, true);
            if (!is_array($data) || array_is_list($data)) {
                throw new AfribaPayNotificationException('Invalid JSON payload.', 400);
            }
            if (isset($data['data']) && is_array($data['data'])) {
                $data = $data['data'];
            }
            foreach (['transaction_id', 'order_id', 'status', 'currency', 'country'] as $field) {
                if (!isset($data[$field]) || !is_string($data[$field]) || $data[$field] === '') {
                    throw new AfribaPayNotificationException('Missing or invalid transaction fields.', 400);
                }
            }
            if (!isset($data['original_amount']) || !is_numeric($data['original_amount'])) {
                throw new AfribaPayNotificationException('Missing or invalid transaction fields.', 400);
            }

            if ($confirmWithApi) {
                $data = self::fetchStatus($config, $data['transaction_id'], $http);
            }

            $payment = new AfribaPayPayment($data);
            if (!$payment->isFinal()) {
                throw new AfribaPayNotificationException('Unsupported transaction status.', 400);
            }
            return $payment;
        }

        private static function fetchStatus(array $config, string $transactionId, ?callable $http = null): array {
            $baseUrl = rtrim(!empty($config['apiUrl']) && is_string($config['apiUrl'])
                ? $config['apiUrl']
                : (strtolower($config['environment'] ?? 'sandbox') === 'production'
                    ? (defined('API_URL') ? API_URL : 'https://api.afribapay.com')
                    : 'https://api-sandbox.afribapay.com'), '/');

            $http = $http ?? function (string $method, string $url, array $headers): array {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST  => $method,
                    CURLOPT_HTTPHEADER     => $headers,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT        => 30,
                ]);
                if ($method === 'POST') {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
                }
                $body = curl_exec($ch);
                $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $errno = curl_errno($ch);
                $error = curl_error($ch);
                curl_close($ch);
                if ($errno !== 0 || $body === false) {
                    $host = parse_url($url, PHP_URL_HOST);
                    throw new AfribaPayNotificationException("Network error while contacting $host: $error (cURL error $errno).", 503);
                }
                return [$code, (string)$body];
            };

            [$code, $body] = $http('POST', $baseUrl . '/v1/token', [
                'Authorization: Basic ' . base64_encode($config['apiUser'] . ':' . $config['apiKey']),
                'Content-Type: application/json',
            ]);
            $json = json_decode((string)$body, true);
            $token = $json['data']['access_token'] ?? null;
            if (!$token) {
                throw new AfribaPayNotificationException("Unable to authenticate with the AfribaPay API (HTTP $code).", 503);
            }

            [$code, $body] = $http('GET', $baseUrl . '/v1/status?' . http_build_query(['transaction_id' => $transactionId]), [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ]);
            $json = json_decode((string)$body, true);
            if ($code !== 200 || !isset($json['data']) || !is_array($json['data'])) {
                throw new AfribaPayNotificationException("Unknown transaction or unexpected status response (HTTP $code).", 503);
            }
            $apiTransactionId = (string)($json['data']['transaction_id'] ?? '');
            if ($apiTransactionId !== '' && !hash_equals($transactionId, $apiTransactionId)) {
                throw new AfribaPayNotificationException('Transaction identifier mismatch.', 400);
            }
            return $json['data'];
        }

        public static function acknowledge(int $httpCode = 200, string $message = 'OK'): void {
            if (!headers_sent()) {
                http_response_code($httpCode);
                header('Content-Type: text/plain; charset=utf-8');
            }
            echo $message;
        }

        private static function signatureHeader(): ?string {
            if (!empty($_SERVER['HTTP_AFRIBAPAY_SIGN'])) {
                return (string)$_SERVER['HTTP_AFRIBAPAY_SIGN'];
            }
            if (function_exists('getallheaders')) {
                foreach ((array)getallheaders() as $name => $value) {
                    if (strcasecmp(str_replace('-', '_', $name), 'AFRIBAPAY_SIGN') === 0) {
                        return (string)$value;
                    }
                }
            }
            return null;
        }

        private static function loadConfig(string $path): array {
            if (!is_file($path)) {
                throw new AfribaPayNotificationException("customer_config.inc.php not found at: $path");
            }
            $config = require $path;
            foreach (['apiUser', 'apiKey', 'environment'] as $key) {
                if (empty($config[$key])) {
                    throw new AfribaPayNotificationException("Missing '$key' in customer_config.inc.php");
                }
            }
            return $config;
        }
    }
}
