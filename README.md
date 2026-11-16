# AfribaPay PHP WebCheckout SDK Documentation

## Introduction

The **AfribaPay PHP WebCheckout SDK** enables developers to integrate AfribaPay's webcheckout payment gateway seamlessly into their applications. When the customer clicks the payment button, they are **redirected to the checkout page hosted by AfribaPay**: nothing to host on your side except the button and your notification endpoint. It simplifies payment initialization, checkout button generation, and response handling while supporting multiple currencies and countries.

Refer to the official documentation for further details: [AfribaPay Documentation](https://docs.afribapay.com).

---

## Installation

### Prerequisites

1. **PHP Version**: Ensure you are using PHP 8.1 or higher (the SDK uses PHP enums).
2. **Extensions**: `json`, `openssl` enabled (`curl` is also needed by the notification helper when `confirmWithApi: true` is used)



### Steps

1. Clone or download the SDK repository:
  ```bash
   git clone https://github.com/afribapay/afribapay-php-checkout-sdk.git
  ```
2. Configure your API credentials in `src/customer_config.inc.php` (see Configuration section below)
3. Add a payment button to your page (see Quick Start below)

---



## Quick Start

```php
<?php
// Include the SDK
require_once __DIR__ . '/src/afribapay.sdk.php';

// Create payment request
$request = new AfribaPayRequest();
$request->description = 'Order #12345'; // Required
$request->amount = 1000;
$request->currency = 'XOF';
$request->country = 'BJ';
$request->order_id = 'ORDER_' . uniqid();

// Create button
$sdk = new AfribaPaySDK();
echo $sdk->createCheckoutButton($request, '💳 Pay Now');
?>
```

When the customer clicks the button, the payment data is posted to the AfribaPay hosted checkout (`checkout-sandbox.afribapay.com/ck/sdk` in sandbox, `checkout.afribapay.com/ck/sdk` in production), where the payment takes place.

---



## Usage Guide



### 1. Configure access

Use the `customer_config.inc.php` file in the `src` directory to configure API identifiers:

```php
    return [
        // The user identifier for the API, typically a public (non-sensitive) key used to identify the client application.
        'apiUser' => 'your_api_user',

        // The secret key used to authenticate API requests.
        // This key should be kept confidential and never exposed publicly.
        'apiKey' => 'your_secret_key',

        // A unique key assigned to the merchant using the API.
        // It helps link requests to the correct merchant account.
        'merchantKey' => 'your_merchant_key',

        // A unique identifier assigned to the agent (partner or vendor).
        // It is used for tracking operations, transactions, and reporting.
        'agent_id' => 'your_agent_id',

        // Defines the current working environment of the API:
        // Use 'production' for live transactions or 'sandbox' for test mode.
        'environment' => 'sandbox',

        // The language used in API responses and user-facing interfaces.
        // Example: 'fr' for French, 'en' for English.
        'lang' => 'fr',

        // Bootstrap CSS used by the payment buttons: 'auto' (public CDNs with browser-side fallback),
        // your own URL, or false if your site already loads Bootstrap 5.
        'bootstrapCss' => 'auto',

    ];
```



### 2. Initialize the SDK

Use the `AfribaPaySDK` class to define a new SDK object (it reads `customer_config.inc.php` and outputs the Bootstrap CSS and the button script once):

```php
$AfribaPayButton = new AfribaPaySDK();
```



### 3. Create a Payment Request

Define transaction details using the `AfribaPayRequest` object:

```php
$request = new AfribaPayRequest();
$request->description = 'Iphone payment'; // Description of your item (REQUIRED)
$request->amount = 100; // Transaction amount
$request->currency = 'XOF'; // Transaction currency
$request->order_id = 'ORDER123'; // Unique order ID
$request->reference_id = 'ref-tfp-bf'; // Reference ID
$request->country = 'BF'; // Transaction country
$request->company = 'My Store'; // Company displayed on the checkout page
$request->checkout_name = 'My Store - Payment'; // Checkout page title
$request->showCountries = true; // Display available countries for payment 
$request->notify_url = "https://www.example.com/checkout/notify"; // URL that the payment system will notify (via POST) about the result/status of the transaction.
$request->return_url = "https://www.example.com/checkout/success"; // Return URL after a successful payment
$request->cancel_url = "https://www.example.com/checkout/cancel"; // Return URL after a failed or cancelled payment
// $request->logo_url = 'https://www.example.com/logo.png'; // Logo displayed on the checkout page (checked: must answer HTTP 200 with an image)
```



### 4. Generate a Checkout Button

Use the `createCheckoutButton` method to generate an HTML button for the payment page:

```php
$buttonHtml = $AfribaPayButton->createCheckoutButton(
    $request, // Payment request object
    'Pay now', // Button text
    '#FF5733', // Button color
    'large' // Button size
);

echo $buttonHtml; // Output the button HTML
```



### 5. Handle Errors

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

Access configuration in customer_config.inc.php

```php
<?php
return [
    'apiUser'           => 'pk_15fb8ccc-e2a8-4350-afad-acbf224f2e64',
    'apiKey'            => 'sk_NA24xhNko7N96XJQZzBd337W33l5Ff5q4jSv1907m',
    'merchantKey'       => 'mk_Dv2c9Us240920061620',
    'agent_id'          => 'APM31923613',
    'environment'       => 'sandbox',
    'lang'              => 'fr',
    'bootstrapCss'      => 'auto'
];
?>
```

Main code

```php
<?php
require 'path_to_sdk/afribapay.sdk.php';

try {
    $AfribaPayButton = new AfribaPaySDK();
    $request = new AfribaPayRequest();
    $request->description = 'Iphone payment';
    $request->amount = 150;
    $request->currency = 'XOF';
    $request->order_id = 'ORDER123';
    $request->reference_id = 'ref-tfp-bf';
    $request->company = 'My Store';
    $buttonHtml = $AfribaPayButton->createCheckoutButton(
        $request, 
        'Payer now', 
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



### Running the examples locally

Start PHP's built-in server from the SDK folder:

```bash
cd afp-sdk && php -S localhost:8000
```

Then open `http://localhost:8000/examples/`.

The server can be started from **any folder** (e.g. a parent folder, or XAMPP/WAMP/MAMP `htdocs`) and on **any OS** (macOS, Linux, Windows): the examples URL is detected automatically.

---



## Supported Currencies

The SDK supports the following currencies:


| Currency Code | Description               |
| ------------- | ------------------------- |
| **XOF**       | West African CFA Franc    |
| **XAF**       | Central African CFA Franc |
| **CDF**       | Congolese Franc           |
| **GNF**       | Guinean Franc             |
| **KES**       | Kenyan Shilling           |
| **MWK**       | Malawian Kwacha           |
| **RWF**       | Rwandan Franc             |
| **SLE**       | Sierra Leonean Leone      |
| **UGX**       | Ugandan Shilling          |
| **ZMW**       | Zambian Kwacha            |


---



## Supported Countries

The SDK supports payments in the following countries:


| Country Code | Currency Code | Country Name        |
| ------------ | ------------- | ------------------- |
| **BJ**       | XOF           | Benin               |
| **BF**       | XOF           | Burkina Faso        |
| **CI**       | XOF           | Côte d'Ivoire       |
| **ML**       | XOF           | Mali                |
| **NE**       | XOF           | Niger               |
| **SN**       | XOF           | Senegal             |
| **TG**       | XOF           | Togo                |
| **CM**       | XAF           | Cameroon            |
| **CF**       | XAF           | Central African Rep |
| **CG**       | XAF           | Republic of Congo   |
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



### `customer_config.inc.php`



#### File properties:


| Parameter      | Type          | Description                                                                                    |
| -------------- | ------------- | ---------------------------------------------------------------------------------------------- |
| `apiUser`      | String        | User identifier for the API                                                                    |
| `apiKey`       | String        | Secret key for authentication                                                                  |
| `agent_id`     | String        | Unique identifier for the agent                                                                |
| `merchantKey`  | String        | Specific key for the merchant account                                                          |
| `environment`  | String        | 'sandbox' or 'production' for API environment                                                  |
| `lang`         | String        | Response language ('en' or 'fr')                                                               |
| `bootstrapCss` | String\|false | `'auto'` (default), your own stylesheet URL, or `false` if your page already loads Bootstrap 5 |




### `AfribaPayRequest`



#### Properties:


| Property        | Type    | Description                                                                                          |
| --------------- | ------- | ---------------------------------------------------------------------------------------------------- |
| `description`   | String  | Payment description (**required**)                                                                   |
| `amount`        | Float   | Payment amount (>= 1)                                                                                |
| `currency`      | String  | Payment currency                                                                                     |
| `order_id`      | String  | Unique order identifier                                                                              |
| `reference_id`  | String  | Reference ID for reconciliation                                                                      |
| `country`       | String  | Country code for the transaction                                                                     |
| `showCountries` | Boolean | Display available countries for payment (default: true)                                             |
| `notify_url`    | String  | Notification URL for this payment (http/https)                                                       |
| `return_url`    | String  | Return URL after a successful payment (http/https)                                                   |
| `cancel_url`    | String  | Return URL after a failed or cancelled payment (http/https)                                          |
| `company`       | String  | Name of the initiating company                                                                       |
| `checkout_name` | String  | Title for the checkout page                                                                          |
| `logo_url`      | String  | Logo URL (must answer HTTP 200 with an image; fetched by your server, a slow host delays your page) |


---



## Project Structure

```
afp-sdk/
├── src/
│   ├── afribapay.sdk.php            # SDK classes (AfribaPaySDK, AfribaPayRequest)
│   ├── afribapay.notification.php   # Notification helper (notify_url)
│   └── customer_config.inc.php      # Your configuration
│   
├── examples/
│   ├── index.php                    # Examples navigation
│   ├── 01-basic-payment.php         # Basic example
│   ├── 06-with-logo.php             # Custom logo
│   ├── 11-notify-handler.php        # Notification handler
│   └── ...
├── tests/                           # php tests/<name>_test.php
└── README.md                        # This file
```

---



## Handling notifications (`notify_url`)

When a transaction reaches a final status (`SUCCESS` or `FAILED`), AfribaPay sends an HTTP POST (JSON) to your `notify_url`, signed in the `AFRIBAPAY_SIGN` header (HMAC-SHA256 of the raw body with your `apiKey`). The SDK verifies it for you:

```php
<?php
require_once 'path/to/src/afribapay.notification.php';

try {
    $payment = AfribaPayNotification::verify();          // POST, signature, JSON, required fields, SUCCESS/FAILED
    // $payment = AfribaPayNotification::verify(confirmWithApi: true); // + check with the Status API
} catch (AfribaPayNotificationException $e) {
    AfribaPayNotification::acknowledge($e->getHttpCode(), $e->getMessage()); // 405, 400, 403 or 503
    exit;
}

// In a database transaction, with the order row locked (SELECT ... FOR UPDATE)
$order = findOrder($payment->orderId);                    // unknown order: answer 400, do nothing
if (alreadyProcessed($payment->transactionId)) {          // duplicate notification: answer 200, do nothing
    AfribaPayNotification::acknowledge();
    exit;
}
if ($payment->isSuccessful()
    && $payment->matchesOrder($order->id, $order->amount, $order->currency, $order->country)) {
    $order->markAsPaid($payment->transactionId);          // never overwrite an already paid order
} elseif ($payment->isFailed()) {
    $order->markAsFailed($payment->transactionId);
}
AfribaPayNotification::acknowledge();                     // HTTP 200 only after the commit
```

- `$payment->amount` is `original_amount` (amount requested, before fees). `matchesOrder()` returns `false` if it is missing.
- Notifications may be delivered **several times, even simultaneously**: use `transactionId` with a lock or a unique constraint to process each one only once.
- A verified final state must never be overwritten: flag conflicting statuses for manual reconciliation.
- The retry policy of AfribaPay is not documented: keep your orders pending until a final status, and reconcile with the Transaction Status API if a notification never arrives.
- `notify_url` must be a public **HTTPS** URL.
- Never deliver an order because the customer came back to `return_url`: only a verified `SUCCESS` notification (or the Status API) confirms the payment.
- A complete example is in `examples/11-notify-handler.php`, and `tests/notify_handler_test.php <url>` replays the test scenarios of the official guide against it.



## Troubleshooting



### Common Issues


| Issue                                                         | Solution                                                                                                    |
| ------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------- |
| `The customer_config.inc.php file was not found`              | The file must stay in `src/`; verify the file path and permissions                                          |
| `Missing required field in config.inc.php file: …`            | Fill `apiUser`, `apiKey`, `agent_id`, `merchantKey`, `environment` and `lang` in `customer_config.inc.php` |
| `Missing required field: description`                         | `description` is required for the payment request                                                          |
| `amount cannot be less than 1`                                | Use an amount >= 1                                                                                          |
| `return_url must be a valid http(s) URL`                      | `notify_url`, `return_url` and `cancel_url` must be absolute `http://` or `https://` URLs                   |
| `logo_url is not a valid image URL`                           | The logo URL must answer HTTP 200 with an image content type and be reachable from your server              |
| `Invalid environment: …`                                      | Use only `'sandbox'` or `'production'`                                                                      |
| `Field 'lang' in config.inc.php must be 'fr' or 'en'`         | Set `lang` to `'fr'` or `'en'`                                                                              |
| Bootstrap loaded twice / CSP blocks inline `onerror`          | Set `bootstrapCss` to `false` or to your own URL in `customer_config.inc.php`                               |
| Notifications rejected with HTTP 403                          | Your web server must pass the `AFRIBAPAY_SIGN` header to PHP, and `apiKey` must be the account's key        |




### Debug Mode

Enable detailed error reporting:

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```

---



## Support & Contact

- Email: [help@afribapay.com](mailto:support@afribapay.com)
- Documentation: [https://docs.afribapay.com](https://docs.afribapay.com)
- 🌐 Website: [https://afribapay.com](https://afribapay.com)

---



## 📄 License

This SDK is licensed under the [MIT License](LICENSE).

---

