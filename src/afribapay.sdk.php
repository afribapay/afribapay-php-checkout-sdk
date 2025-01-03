<?php
// Allows clients to replace AfribaPay's logo with their own.
class AfribaPayException extends \Exception {}

class Configuration {
    private string $environment;
    private array $urls;

    public function __construct(string $environment = 'production') {
        $this->environment = strtolower($environment);
        $this->urls = [
            'production' => [
                'baseURL' => 'https://api.afribapay.io',
                'checkout' => 'https://checkout.afribapay.com/v1/'
            ],
            'sandbox' => [
                'baseURL' => 'https://api-sandbox.afribapay.com',
                'checkout' => 'https://checkout-sandbox.afribapay.com/v1/'
            ]
        ];
    }

    public function getCheckoutUrl(): string {
        return $this->urls[$this->environment]['checkout'] 
            ?? throw new AfribaPayException("Invalid environment");
    }

    public function getBaseUrl(): string {
        return $this->urls[$this->environment]['baseURL'] 
            ?? throw new AfribaPayException("Invalid environment");
    }
}

class PaymentRequest {
    private array $requiredFields = ['description'];
    private array $allowedFields = [
        'amount', 'currency', 'description', 'order_id', 'reference_id',
        'notify_url', 'return_url', 'cancel_url', 'country', 'showCountries',
        'company', 'checkout_name', 'logo_url'
    ];
    private array $data = [];

    public function __set(string $name, $value): void {
        if (!in_array($name, $this->allowedFields)) {
            throw new AfribaPayException("Field '$name' is not allowed");
        }
        if (empty($value)) {
            throw new AfribaPayException("$name cannot be empty");
        }
        if ($name === 'amount' && $value < 1) {
            throw new AfribaPayException("$name cannot be less than 1");
        }
        if ($name === 'logo_url' && !$this->validateImageUrl($value)) {
            throw new AfribaPayException("$name is not a valid image URL");
        }
        $this->data[$name] = $this->sanitizeInput($value);
    }

    public function __get(string $name) {
        return $this->data[$name] ?? null;
    }

    private function validateImageUrl(string $url): bool {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        $headers = @get_headers($url, 1);
        if (!$headers || strpos($headers[0], '200') === false) {
            return false;
        }
        $validImageTypes = [
            'image/jpeg', 'image/png', 'image/gif', 
            'image/bmp', 'image/webp', 'image/svg+xml'
        ];
        $contentType = $headers['Content-Type'] ?? null;
        if (is_array($contentType)) {
            $contentType = $contentType[0];
        }
        return in_array($contentType, $validImageTypes);
    }

    private function sanitizeInput($value): string {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    public function validate(): void {
        foreach ($this->requiredFields as $field) {
            if (!isset($this->data[$field])) {
                throw new AfribaPayException("Missing required field: $field");
            }
        }
    }

    public function toArray(): array {
        return $this->data;
    }
}

enum Environment: string {
    case Sandbox = 'sandbox';
    case Production = 'production';
}

class AfribaPaySDKClass {
    private Configuration $config;
    private string $merchantKey;
    private string $agent_id;
    private string $apiUser;
    private string $apiKey;
    private string $lang;

    public function __construct(
        string $apiUser,
        string $apiKey,
        string $agent_id,
        string $merchantKey,
        string $environment = 'production',
        string $lang = 'en'
    ) {
        $this->validateCredentials($agent_id, $merchantKey, $apiUser, $apiKey);
        $this->agent_id = $agent_id;
        $this->merchantKey = $merchantKey;
        $this->apiUser = $this->local_encrypt($apiUser);
        $this->apiKey = $this->local_encrypt($apiKey);
        $this->lang = $lang;

        if (!Environment::tryFrom($environment)) {
            throw new InvalidArgumentException("Invalid environment: $environment");
        }
        $this->config = new Configuration($environment);
    }

    private function validateCredentials(string $agent_id, string $merchantKey, string $apiUser, string $apiKey): void {
        if (empty($agent_id) || empty($merchantKey) || empty($apiUser) || empty($apiKey)) {
            throw new AfribaPayException('Invalid credentials provided');
        }
    }

    public function createCheckoutButton(
        PaymentRequest $request,
        string $buttonText = 'Pay',
        string $buttonColor = '#4CAF50',
        string $size = 'medium',
        array $additionnalClass = []
    ): string {
        $request->validate();
        return $this->generateCheckoutForm($request, $buttonText, $buttonColor, $size, $additionnalClass);
    }

    private function generateCheckoutForm(
        PaymentRequest $request,
        string $buttonText,
        string $buttonColor,
        string $size,
        array $additionnalClass
    ): string {
        $formId = $this->generateFormId($buttonText);
        $html = $this->getButtonStyles();
        $html .= sprintf('<form id="%s" action="%s" method="post">', $formId, $this->config->getCheckoutUrl());
        $formData = array_merge(
            $request->toArray(),
            [
                'baseUrl' => $this->config->getBaseUrl(),
                'apiUser' => $this->apiUser,
                'apiKey' => $this->apiKey,
                'agent_id' => $this->agent_id,
                'merchantKey' => $this->merchantKey,
                'lang' => $this->lang,
                'afribapaySdkPost' => time(),
            ]
        );
        foreach ($formData as $key => $value) {
            $html .= $this->createHiddenInput($key, $value);
        }
        $html .= $this->createSubmitButton($formId, $buttonText, $buttonColor, $size, $additionnalClass);
        $html .= '</form>';
        return $html;
    }

    private function generateFormId(string $buttonText): string {
        return strtoupper($buttonText) . '_@_' . random_int(0, 9999);
    }

    private function getButtonStyles(): string {
        return '
        <style>
            .afp-custom-pay-button {
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 12px 24px;
                font-size: 16px;
                cursor: pointer;
                border-radius: 8px;
                transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                font-weight: bold;
            }
            .afp-custom-pay-button:hover {
                background-color: #45a049;
                transform: translateY(-2px);
                box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15);
            }
            .afp-custom-pay-button:active {
                background-color: #3e8e41;
                transform: translateY(1px);
                box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            }
            .afp-custom-pay-button.small {
                padding: 5px 10px;
                font-size: 12px;
            }
            .afp-custom-pay-button.medium {
                padding: 10px 20px;
                font-size: 16px;
            }
            .afp-custom-pay-button.large {
                padding: 15px 30px;
                font-size: 20px;
            }
        </style>';
    }

    private function createHiddenInput(string $name, $value): string {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
        );
    }

    private function createSubmitButton(
        string $formId,
        string $buttonText,
        string $buttonColor,
        string $size,
        array $additionnalClass
    ): string {
        $sizeClass = match (strtolower($size)) {
            'small' => 'small',
            'large' => 'large',
            default => 'medium',
        };
        $extraClasses = implode(' ', array_map('htmlspecialchars', $additionnalClass));
        $allClasses = trim("afp-custom-pay-button $sizeClass $extraClasses");
        return sprintf(
            '<button type="submit" style="background-color: %s;" class="%s" onclick="document.forms[\'%s\'].submit();">%s</button>',
            htmlspecialchars($buttonColor, ENT_QUOTES, 'UTF-8'),
            $allClasses,
            htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($buttonText, ENT_QUOTES, 'UTF-8')
        );
    }

    private function local_encrypt(string $plainText): string {
        $SECRET_KEY = $this->agent_id . '|' . $this->merchantKey;
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($plainText, 'aes-256-cbc', $SECRET_KEY, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
}
