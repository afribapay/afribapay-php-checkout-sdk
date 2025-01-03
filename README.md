# AfribaPay PHP WebCheckout SDK Documentation

## Introduction
The **AfribaPay PHP WebCheckout SDK** enables developers to integrate AfribaPay's payment gateway seamlessly into their applications. It simplifies payment initialization, checkout button generation, and response handling while supporting multiple currencies and countries.

Refer to the official documentation for further details: [AfribaPay Documentation](https://docs.afribapay.com).

---

## Installation

### Prerequisites
1. **PHP Version**: Ensure you are using PHP 7.4 or higher.
2. **Composer**: While the SDK can be manually included, Composer simplifies dependency management.

### Steps
1. Clone or download the SDK repository:
   ```bash
   git clone https://github.com/afribapay/afribapay-php-checkout-sdk.git
   ```

2. Include the SDK in your project:
   ```php
   $sdkPath = dirname(__FILE__, 2) . '/src/afribapay.sdk.php';
   if (file_exists($sdkPath)) {
       require_once($sdkPath);
   } else {
       die("The SDK file was not found at the specified location: " . $sdkPath);
   }
   ```

---

## Usage Guide

### 1. Initialize the SDK
Use the `AfribaPaySDKClass` to set up API credentials:

```php
$AfribaPayButton = new AfribaPaySDKClass(
    apiUser: 'your_api_user',
    apiKey: 'your_secret_key',
    agent_id: 'your_agent_id',
    merchantKey: 'your_merchant_key',
    environment: 'sandbox', // or 'production'
    lang: 'fr' // Language for API responses ('fr', 'en', etc.)
);
```

### 2. Create a Payment Request
Define transaction details using the `PaymentRequest` object:

```php
$request = new PaymentRequest();
$request->amount = 100; // Transaction amount
$request->currency = 'XOF'; // Transaction currency
$request->description = 'Iphone payment';
$request->order_id = 'ORDER123'; // Unique order ID
$request->reference_id = 'ref-tfp-bf'; // Reference ID
$request->country = 'BF'; // Transaction country
$request->company = "WIKI BI Test"; // Company initiating the transaction
$request->checkout_name = "Voiture"; // Checkout page title
$request->notify_url = 'https://example.com/notification_url'; // Notification URL
$request->return_url = 'https://example.com/success'; // Success URL (optional)
$request->cancel_url = 'https://example.com/cancel'; // Cancel URL (optional)
$request->logo_url = 'https://static.cdnlogo.com/logos/i/80/internet-society.svg'; // Logo URL (optional)
$request->showCountries = true; // Display available countries for payment
```

### 3. Generate a Checkout Button
Use the `createCheckoutButton` method to generate an HTML button for the payment page:

```php
$buttonHtml = $AfribaPayButton->createCheckoutButton(
    $request, // Payment request object
    'Payer maintenant', // Button text
    '#FF5733', // Button color
    'large' // Button size
);

echo $buttonHtml; // Output the button HTML
```

### 4. Handle Errors
The SDK provides structured error handling for better debugging:

```php
try {
    // Payment logic here
} catch (AfribaPayException $e) {
    echo "AfribaPayException: " . $e->getMessage();
} catch (Exception $e) {
    echo "UnknownException: " . $e->getMessage();
}
```

---

## Full PHP Example

```php
<?php
require 'path_to_sdk/afribapay.sdk.php';

try {
    $AfribaPayButton = new AfribaPaySDKClass(
        apiUser: 'pk_15fb8ccc-e2a8-4350-afad-acbf224f2e64',
        apiKey: 'sk_NA24xhNko7N96XJQZzBd337W33l5Ff5q4jSv1907m',
        agent_id: 'APM31923613',
        merchantKey: 'mk_Dv2c9Us240920061620',
        environment: 'sandbox',
        lang: 'fr'
    );

    $request = new PaymentRequest();
    $request->amount = 100;
    $request->currency = 'XOF';
    $request->description = 'Iphone payment';
    $request->order_id = 'ORDER123';
    $request->reference_id = 'ref-tfp-bf';
    $request->country = 'BF';
    $request->company = "WIKI BI Test";
    $request->checkout_name = "Voiture";
    $request->notify_url = 'https://example.com/notification_url';
    $request->return_url = 'https://example.com/success';
    $request->cancel_url = 'https://example.com/cancel';
    $request->logo_url = 'https://static.cdnlogo.com/logos/i/80/internet-society.svg';
    $request->showCountries = true;

    $buttonHtml = $AfribaPayButton->createCheckoutButton(
        $request,
        'Payer maintenant',
        '#FF5733',
        'large'
    );

    echo $buttonHtml;

} catch (AfribaPayException $e) {
    echo "AfribaPayException: " . $e->getMessage();
} catch (Exception $e) {
    echo "UnknownException: " . $e->getMessage();
}
```

---

## Supported Currencies

The SDK supports the following currencies:

| Currency Code | Description                     |
|---------------|---------------------------------|
| **XOF**       | West African CFA Franc         |
| **XAF**       | Central African CFA Franc      |
| **CDF**       | Congolese Franc                |
| **GNF**       | Guinean Franc                  |
| **KES**       | Kenyan Shilling                |
| **MWK**       | Malawian Kwacha                |
| **RWF**       | Rwandan Franc                  |
| **SLE**       | Sierra Leonean Leone           |
| **UGX**       | Ugandan Shilling               |
| **ZMW**       | Zambian Kwacha                 |

---

## Supported Countries
The SDK supports payments in the following countries:

| Country Code | Currency Code | Country Name         |
|--------------|---------------|---------------------|
| **BJ**       | XOF           | Benin               |
| **BF**       | XOF           | Burkina Faso        |
| **CI**       | XOF           | Côte d’Ivoire      |
| **GW**       | XOF           | Guinea-Bissau       |
| **ML**       | XOF           | Mali                |
| **NE**       | XOF           | Niger               |
| **SN**       | XOF           | Senegal             |
| **TG**       | XOF           | Togo                |
| **CM**       | XAF           | Cameroon            |
| **CF**       | XAF           | Central African Rep |
| **TD**       | XAF           | Chad                |
| **CG**       | XAF           | Republic of Congo   |
| **GQ**       | XAF           | Equatorial Guinea   |
| **GA**       | XAF           | Gabon               |
| **CD**       | CDF           | DR Congo            |
| **GN**       | GNF           | Guinea              |
| **KE**       | KES           | Kenya               |
| **MW**       | MWK           | Malawi              |
| **RW**       | RWF           | Rwanda              |
| **SL**       | SLE           | Sierra Leone        |
| **UG**       | UGX           | Uganda              |
| **ZM**       | ZMW           | Zambia              |

---

## API Reference

### `AfribaPaySDKClass`
#### Constructor Parameters:
| Parameter       | Type   | Description                                           |
|-----------------|--------|-------------------------------------------------------|
| `apiUser`       | String | User identifier for the API                          |
| `apiKey`        | String | Secret key for authentication                        |
| `agent_id`       | String | Unique identifier for the agent                     |
| `merchantKey`   | String | Specific key for the merchant account               |
| `environment`   | String | 'sandbox' or 'production' for API environment       |
| `lang`          | String | Response language ('en', 'fr', etc.)                |

### `PaymentRequest`
#### Properties:
| Property          | Type    | Description                                              |
|-------------------|---------|----------------------------------------------------------|
| `amount`          | Float   | Payment amount                                           |
| `currency`        | String  | Payment currency                                         |
| `description`     | String  | Payment description                                      |
| `order_id`         | String  | Unique order identifier                                  |
| `reference_id`     | String  | Reference ID for reconciliation                         |
| `country`         | String  | Country code for the transaction                        |
| `company`         | String  | Name of the initiating company                          |
| `checkout_name`    | String  | Title for the checkout page                             |
| `notify_url`      | String  | URL for payment status notifications                    |
| `return_url`      | String  | URL to redirect users upon successful payment (optional)|
| `cancel_url`      | String  | URL to redirect users upon payment cancellation (optional)|
| `logo_url`        | String  | URL for the logo displayed on the payment page (optional)|
| `showCountries`   | Boolean | Enable display of available countries on checkout       |

---

## Contributions
We welcome contributions to enhance the AfribaPay PHP SDK. Fork the repository, create a feature branch, and submit a pull request.

---

## License
This SDK is licensed under the [MIT License](LICENSE).

