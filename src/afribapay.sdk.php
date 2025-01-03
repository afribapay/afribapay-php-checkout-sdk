<?php
// Permettre au client de mettre son logo a la place de celui de afribapay.
class AfribaPayException extends \Exception {}

class Configuration {
    private string $environment;
    private array $urls;
    
    public function __construct(string $environment = 'production') {
        $this->environment = strtolower($environment);
        $this->urls = [
            'production' => [
                'baseURL' => 'https://api.afribapay.com',
                'checkout' => 'https://checkout.afribapay.com/v1/'
            ],
            'sandbox' => [
                'baseURL' => 'https://api-sandbox.afribapay.com',
                'checkout' => 'https://checkout-sandbox.afribapay.com/v1/'
            ]
        ];
    }

    public function getCheckoutUrl(): string {
        return $this->urls[$this->environment]['checkout'] ?? throw new AfribaPayException("Invalid environment");
    }

    public function getBaseUrl(): string {
        return $this->urls[$this->environment]['baseURL'] ?? throw new AfribaPayException("Invalid environment");
    }
}

class PaymentRequest {
    private $requiredFields = ['description'];
    private $allowedFields = [
        'amount', 'currency', 'description', 'orderId', 'referenceId',
        'notify_url', 'return_url', 'cancel_url', 'country','showCountries',
        'company', 'checkoutName', 'logo_url'
    ];
    private $data = [];
    
    public function __set($name, $value) {
        if (!in_array($name, $this->allowedFields)) {
            throw new AfribaPayException("Field '$name' is not allowed");
        }
        if ($value === null || $value === '') {
            throw new AfribaPayException("$name cannot be empty");
        }
        if($name == 'amount' && $value < 1){
            throw new AfribaPayException("$name cannot be less than 1");
        }
        if($name == 'logo_url'){
            if(!$this->validateImageUrl($value)) {
                throw new AfribaPayException("$name is not a valid image url");
            }
        }
        $this->data[$name] = $this->sanitizeInput($value);
    }
    
    public function __get($name) {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    function validateImageUrl($url) {
        // Validate if the URL is well-formed
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
    
        // Retrieve headers to validate content type and availability
        $headers = @get_headers($url, 1);
    
        // Ensure the headers exist and the HTTP status is 200 (OK)
        if (!$headers || strpos($headers[0], '200') === false) {
            return false;
        }
    
        // Define the valid image MIME types, including SVG support
        $validImageTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/webp',
            'image/svg+xml' // Support for SVG
        ];
    
        // Check the Content-Type header for a valid MIME type
        if (isset($headers['Content-Type'])) {
            $contentType = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];
            if (in_array($contentType, $validImageTypes)) {
                return true;
            }
        }
    
        // If no valid MIME type is found, return false
        return false;
    }

    private function sanitizeInput($value) {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
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

    public function getAllowedFields() {
        return $this->allowedFields;
    }

    public function getRequiredFields() {
        return $this->requiredFields;
    }
}

enum Environment {
    case Sandbox;
    case Production;
    case Dev;
};
class AfribaPaySDKClass {
    private Configuration $config;
    private string $merchantKey;
    private string $agentId;
    private string $apiUser;
    private string $apiKey;
    private string $lang;

    public function __construct(
        string $apiUser,
        string $apiKey,
        string $agentId,
        string $merchantKey,
        string $environment = 'production',
        string $lang = 'en'
    ) {
        $this->validateCredentials($agentId, $merchantKey, $apiUser, $apiKey);
        
        $this->agentId = $agentId;
        $this->merchantKey = $merchantKey;
        $this->apiUser = $this->local_encrypt($apiUser);
        $this->apiKey = $this->local_encrypt($apiKey);
        $this->lang = $lang ?? 'en';

        match ($environment) {
            'production' => Environment::Production,
            'sandbox' => Environment::Sandbox,
            'dev' => Environment::Dev,
            default => throw new InvalidArgumentException("Invalid environment: $environment"),
        };

        $this->config = new Configuration($environment);
    }
    
    private function validateCredentials(string $agentId, string $merchantKey, string $apiUser, string $apiKey): void {
        if (empty($agentId) || empty($merchantKey) || empty($apiUser) || empty($apiKey)) {
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
        try {
            $request->validate();
            return $this->generateCheckoutForm($request, $buttonText, $buttonColor, $size, $additionnalClass);
        } catch (\Exception $e) {
            throw new AfribaPayException("Failed to create checkout button: " . $e->getMessage());
        }
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
        $html .= sprintf(
            '<form id="%s" action="%s" method="post">',
            $formId,
            $this->config->getCheckoutUrl()
        );
        
        $formData = array_merge(
            $request->toArray(),
            [
                'baseUrl' => $this->config->getBaseUrl(),
                'apiUser' => $this->apiUser,
                'apiKey' => $this->apiKey,
                'agentId' => $this->agentId,
                'merchantKey' => $this->merchantKey,
                'lang' => $this->lang,
                'afribapaySdkPost' => time(),
                'showCountries' => $request->showCountries ?? true,
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
                transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
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
    
    private function createSubmitButton(string $formId, string $buttonText, string $buttonColor, string $size, array $additionnalClass): string {
        $sizeClass = $this->getButtonSizeClass($size);
        // Joindre les classes supplémentaires
        $extraClasses = implode(' ', array_map('htmlspecialchars', $additionnalClass));
        // Combiner les classes
        $allClasses = trim("afp-custom-pay-button $sizeClass $extraClasses");
        return sprintf(
            '<button type="submit" style="background-color: %s;" class="%s" onclick="javascript:document.forms[\'%s\'].submit();">%s</button>',
            htmlspecialchars($buttonColor, ENT_QUOTES, 'UTF-8'),
            $allClasses,
            htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($buttonText, ENT_QUOTES, 'UTF-8')
        );
    }
    
    private function getButtonSizeClass(string $size): string {
        return match (strtolower($size)) {
            'small' => 'small',
            'large' => 'large',
            default => 'medium',
        };
    }

    private function local_encrypt(string $plaintText): string {
        $SECRET_KEY = $this->agentId.'|'. $this->merchantKey;
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($plaintText, 'aes-256-cbc', $SECRET_KEY, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

}