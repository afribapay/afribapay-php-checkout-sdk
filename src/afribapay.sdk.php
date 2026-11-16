<?php

if (!defined('CUSTOMER_CONFIG_PATH')) {
    define("CUSTOMER_CONFIG_PATH", dirname(__FILE__) . '/customer_config.inc.php');
}
if (!class_exists('AfribaPayException')) {
    class AfribaPayException extends \Exception {}
}

if (!function_exists('sdk_afp_endpoint')) {
    function sdk_afp_endpoint(string $service, ?string $environment = null): string {
        static $config = null;
        if ($config === null) {
            $config = is_file(CUSTOMER_CONFIG_PATH) ? (require CUSTOMER_CONFIG_PATH) : [];
            $config = is_array($config) ? $config : [];
        }
        $production = strtolower($environment ?? ($config['environment'] ?? 'sandbox')) === 'production';
        $urls = $production
            ? ['api' => defined('API_URL') ? API_URL : 'https://api.afribapay.com',
               'cdn' => 'https://cdn.afribapay.com',
               'checkout' => 'https://checkout.afribapay.com']
            : ['api' => 'https://api-sandbox.afribapay.com',
               'cdn' => 'https://cdn-sandbox.afribapay.com',
               'checkout' => 'https://checkout-sandbox.afribapay.com'];
        $override = $config[$service . 'Url'] ?? null;
        $url = is_string($override) && $override !== '' ? $override : ($urls[$service] ?? null);
        if ($url === null) {
            throw new AfribaPayException("Unknown AfribaPay service: $service");
        }
        return rtrim($url, '/');
    }
}
enum AfpEnvironment: string {
    case Sandbox = 'sandbox';
    case Production = 'production';
}
class AfpConfiguration {
    private string $environment;
    private array $urls;
    private $params;
    
    public function __construct() {
        $this->validateConfigInc();
        $this->environment = $this->getConfigInc('environment');
        if(!AfpEnvironment::tryFrom($this->environment)){
            throw new InvalidArgumentException("Invalid environment: $this->environment");
        }
        $this->urls = [];
        foreach (['production', 'sandbox'] as $environment) {
            $this->urls[$environment] = [
                'baseURL'  => sdk_afp_endpoint('api', $environment),
                'checkout' => sdk_afp_endpoint('checkout', $environment) . '/ck/sdk',
            ];
        }
    }

    private function validateConfigInc() {

        if (!file_exists(CUSTOMER_CONFIG_PATH)) {
            throw new AfribaPayException("The customer_config.inc.php file was not found at: " . CUSTOMER_CONFIG_PATH); 
        }

        $params = require CUSTOMER_CONFIG_PATH;
        if (!is_array($params)) {
            throw new AfribaPayException("Invalid customer_config.inc.php file");
        }
        $this->params = $params;
        $requiredFields = [
            'apiUser', 
            'apiKey', 
            'agent_id', 
            'merchantKey', 
            'environment', 
            'lang'
        ];
        foreach ($requiredFields as $field) {
            if (!isset($this->params[$field]) || empty($this->params[$field])) {
                throw new AfribaPayException("Missing required field in config.inc.php file: $field");
            }
        }
        $this->params['apiUser'] = $this->local_encrypt($this->params['apiUser']);
        $this->params['apiKey'] = $this->local_encrypt($this->params['apiKey']);
        if (!in_array($params['lang'], ['en', 'fr'])) {
            throw new AfribaPayException("Field 'lang' in config.inc.php must be 'fr' or 'en' ");
        }
    }

    public function getConfigInc($key){
        return $this->params[$key] ?? null;
    }

    public function getCheckoutUrl(): string {
        return $this->urls[$this->environment]['checkout'] 
        ?? throw new AfribaPayException("Invalid environment");
    }

    public function getBaseUrl(): string {
        return $this->urls[$this->environment]['baseURL'] 
        ?? throw new AfribaPayException("Invalid environment");
    }

    private function local_encrypt(string $plainText): string {
        $SECRET_KEY = $this->params['agent_id'] . '|' . $this->params['merchantKey']; 
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($plainText, 'aes-256-cbc', $SECRET_KEY, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    
}
class AfribaPayRequest {
    private $requiredFields = ['description'];
    private $allowedFields = [
        'amount', 
        'currency', 
        'order_id', 
        'reference_id', 
        'country', 
        'showCountries', 
        'notify_url',

        'description',
        'company',
        'checkout_name',
        'logo_url',
        'return_url',
        'cancel_url',
    ];

    private $data = [];

    public function __set($name, $value) {
        if (!in_array($name, $this->allowedFields)) {
            throw new AfribaPayException("Field '$name' is not allowed");
        }
        if ($name === 'showCountries') {
            if (!is_bool($value)) {
                throw new AfribaPayException("$name must be true or false");
            }
            $this->data[$name] = $value ? 1 : 0;
            return;
        }
        if (empty($value)) {
            throw new AfribaPayException("$name cannot be empty");
        }
        if($name == 'amount' && $value < 1){
            throw new AfribaPayException("$name cannot be less than 1");
        }
        if (in_array($name, ['notify_url', 'return_url', 'cancel_url'], true) && !$this->isHttpUrl($value)) {
            throw new AfribaPayException("$name must be a valid http(s) URL");
        }
        if ($name === 'logo_url' && !$this->validateImageUrl($value)) {
            throw new AfribaPayException("$name is not a valid image URL");
        }
        $this->data[$name] = $this->sanitizeInput($value);
    }

    public function __get($name) {
        return $this->data[$name] ?? null;
    }

    private function validateImageUrl($url) {
        if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
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

    private function isHttpUrl($url): bool {
        if (!is_string($url) || !filter_var(trim($url), FILTER_VALIDATE_URL)) {
            return false;
        }
        return in_array(strtolower((string)parse_url(trim($url), PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    // Stored raw: escaping here broke URLs ("&" became "&amp;"). Escaped on output.
    private function sanitizeInput($value) {
        return is_string($value) ? strip_tags(trim($value)) : $value;
    }
    
    public function validate() {
        foreach ($this->requiredFields as $field) {
            if (!isset($this->data[$field])) {
                throw new AfribaPayException("Missing required field: $field");
            }
        }
    }
    
    public function toArray() {
        return $this->data;
    }
}
class AfribaPaySDK {
    private AfpConfiguration $config;
    private string $lang;
    private static bool $bootstrapLoaded = false;
    private static bool $formScriptLoaded = false;

    public function __construct() {
        $this->config = new AfpConfiguration();
        $this->lang =  $this->config->getConfigInc('lang');
        print $this->getBootstrapLink();
        print $this->getFormScript();
    }

    public function createCheckoutButton(
        AfribaPayRequest $request,
        string $buttonText = 'Pay',
        string $buttonColor = '#4CAF50',
        string $size = 'medium',
        array $additionnalClass = []
    ): string {
        $request->validate();
        $formId = 'afp-form-'.uniqid();
        $html = sprintf('<form id="%s" data-afp-data="%s" class="afp-payment-form">', 
            $formId,
            htmlspecialchars(base64_encode(json_encode($this->prepareHiddenInputs($request->toArray()))), ENT_QUOTES, 'UTF-8')
        );
        $html .= $this->createSubmitButton($formId, $buttonText, $buttonColor, $size, $additionnalClass);
        $html .= '</form>';
        return $html;
    }

    private function prepareHiddenInputs(array $data): array {
        $merchantKey = $this->config->getConfigInc('merchantKey');
        return array_merge(
            $data,
            [
                'baseUrl' => $this->config->getBaseUrl(),
                'apiUser' => $this->config->getConfigInc('apiUser'),
                'apiKey' => $this->config->getConfigInc('apiKey'),
                'agent_id' => $this->config->getConfigInc('agent_id'),
                'merchantKey' => $merchantKey,
                'lang' => $this->lang,
                'sdkId' => 'sdk_' . bin2hex(random_bytes(16)),
                'showCountries' => $data['showCountries'] ?? true
            ]
        );
    }

    private function getFormScript(): string {
        if (self::$formScriptLoaded) { return '';}
        self::$formScriptLoaded = true;
        $checkoutUrl = htmlspecialchars($this->config->getCheckoutUrl(), ENT_QUOTES, 'UTF-8');
        $script = '(function(){function a(){var b=document.querySelectorAll("form.afp-payment-form");b.forEach(function(c){if(c.hasAttribute("data-afp-initialized"))return;c.setAttribute("data-afp-initialized","true");var d=c.querySelector("button[type=\'submit\']");if(!d)return;d.addEventListener("click",function(e){e.preventDefault();var f=c.getAttribute("data-afp-data");if(!f){console.error("AfribaPay: Missing data");return}var g=c.querySelector("input[name=\'afp_data\']");if(g)g.remove();var h=document.createElement("input");h.type="hidden";h.name="afp_data";h.value=f;c.appendChild(h);c.action="' . $checkoutUrl . '";c.method="post";c.submit()})})}if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",a)}else{a()}})();';
        return '<script>' . $script . '</script>';
    }

    private function createSubmitButton(
        string $formId,
        string $buttonText,
        string $buttonColor,
        string $size,
        array $additionnalClass
    ): string {
        $sizeClass = $this->getSizeClass($size);
        $colorClass = $this->getColorClass($buttonColor);
        $extraClasses = implode(' ', array_map('htmlspecialchars', $additionnalClass));
        $allClasses = trim("btn {$colorClass} {$sizeClass} afp-payment-btn {$extraClasses}");
        $inlineStyle = $this->isCustomColor($buttonColor) 
            ? sprintf('style="background-color: %s !important; border-color: %s !important; color: white;"', 
                htmlspecialchars($buttonColor, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($buttonColor, ENT_QUOTES, 'UTF-8')
              )
            : '';
        return sprintf(
            '<button type="submit" class="%s" %s>%s</button>',
            $allClasses,
            $inlineStyle,
            htmlspecialchars($buttonText, ENT_QUOTES, 'UTF-8')
        );
    }

    private const BOOTSTRAP_CDN_URLS = [
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css',
        'https://unpkg.com/bootstrap@5.3.8/dist/css/bootstrap.min.css',
    ];

    private function getBootstrapLink(): string {
        if (self::$bootstrapLoaded) {
            return '';
        }
        self::$bootstrapLoaded = true;

        $option = $this->config->getConfigInc('bootstrapCss') ?? 'auto';
        if ($option === false || $option === 'false' || $option === '') {
            return '';
        }

        if ($option !== 'auto' && is_string($option)) {
            return sprintf(
                '<link id="afp-bootstrap-css" rel="stylesheet" href="%s">',
                htmlspecialchars($option, ENT_QUOTES, 'UTF-8')
            );
        }

        $urls      = self::BOOTSTRAP_CDN_URLS;
        $first     = array_shift($urls);
        $fallbacks = implode('|', $urls);

        // On load error, the browser switches to the next CDN
        $onError = "var f=(this.dataset.fallbacks||'').split('|').filter(Boolean);"
                 . "if(f.length){this.dataset.fallbacks=f.slice(1).join('|');this.href=f[0];}";

        return sprintf(
            '<link id="afp-bootstrap-css" rel="stylesheet" href="%s" data-fallbacks="%s" onerror="%s">',
            htmlspecialchars($first, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($fallbacks, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($onError, ENT_QUOTES, 'UTF-8')
        );
    }

    private function getSizeClass(string $size): string {
        return match (strtolower($size)) {
            'small', 'sm' => 'btn-sm',
            'large', 'lg' => 'btn-lg',
            'medium', 'md', '' => 'btn-md',
            default => 'btn-md',
        };
    }

    private function getColorClass(string $color): string {
        $colorMap = [
            'success' => 'btn-success',
            'primary' => 'btn-primary',
            'secondary' => 'btn-secondary',
            'danger' => 'btn-danger',
            'warning' => 'btn-warning',
            'info' => 'btn-info',
            'dark' => 'btn-dark',
            'light' => 'btn-light',
        ];
        return $colorMap[$color] ?? $color;
    }

    private function isCustomColor(string $color): bool {
        return !in_array(strtolower($color), ['success', 'primary', 'secondary', 'danger', 'warning', 'info', 'dark', 'light']);
    }

}
