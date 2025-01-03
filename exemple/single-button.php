<?php
/**
 ***
 *** Refer to https://docs.afribapay.com for extra documentation
 ***
 **/
$sdkPath = dirname(__FILE__,2).'/src/afribapay.sdk.php';
if (file_exists($sdkPath)) {
    require_once($sdkPath);
 } else {
    die("The SDK file was not found at the specified location : " . $sdkPath);
}

try {

    $AfribaPayButton = new AfribaPaySDKClass(
    
        // The user identifier for the API, typically a public key.
        apiUser: 'pk_15fb8ccc-e2a8-4350-afad-acbf224f2e64',
        
        // The secret key for authenticating API requests, keep this secure.
        apiKey: 'sk_NA24xhNko7N96XJQZzBd337W33l5Ff5q4jSv1907m',
        
        // The unique identifier for the agent, used to track transactions or activities.
        agentId: 'APM31923613',
        
        // The merchant key, specific to the account of the merchant using the API.
        merchantKey: 'mk_Dv2c9Us240920061620',
        
        // Specifies the environment where the API is running: 'production' for live usage, or 'sandbox' for testing.
        environment: 'sandbox', // 'production' or 'sandbox'
        
        // Sets the language for API responses, here 'fr' for French.
        lang: 'fr'
    );
    
    // Create a new instance of the PaymentRequest object to define payment details.
    $request = new PaymentRequest();
    
    // Set the amount to be paid.
    $request->amount = 100;
    
    // Define the currency for the payment (e.g., XOF - West African CFA franc, XAF - Central African CFA franc, CDF - Congolese Franc, GNF - Guinean Franc, KES - Kenyan Shilling, MWK - Malawian Kwacha, RWF - Rwandan Franc, SLE - Sierra Leonean Leone, UGX - Ugandan Shilling, ZMW - Zambian Kwacha).
    $request->currency = 'XOF';

    // Add a description for the payment, which will appear on the payment interface.
    $request->description = 'Iphone payment';
    
    // Set the unique order ID to track the payment.
    $request->orderId = 'ORDER123';
    
    // Provide a reference ID for the transaction, useful for reconciliation.
    $request->referenceId = 'ref-tfp-bf';
    
    // Specify the country code for the transaction, based on the currency:
    // XOF - West African CFA franc (BJ - Benin, BF - Burkina Faso, CI - Côte d'Ivoire, GW - Guinea-Bissau, ML - Mali, NE - Niger, SN - Senegal, TG - Togo).
    // XAF - Central African CFA franc (CM - Cameroon, CF - Central African Republic, TD - Chad, CG - Republic of Congo, GQ - Equatorial Guinea, GA - Gabon).
    // CDF - Congolese Franc (CD - Democratic Republic of Congo).
    // GNF - Guinean Franc (GN - Guinea).
    // KES - Kenyan Shilling (KE - Kenya).
    // MWK - Malawian Kwacha (MW - Malawi).
    // RWF - Rwandan Franc (RW - Rwanda).
    // SLE - Sierra Leonean Leone (SL - Sierra Leone).
    // UGX - Ugandan Shilling (UG - Uganda).
    // ZMW - Zambian Kwacha (ZM - Zambia).
    $request->country = 'BF';
    
    // Define the company name initiating the transaction.
    $request->company = "WIKI BI Test";
    
    // Set the checkout name that appears on the payment page.
    $request->checkoutName = "Voiture";
    
    // Specify the URL for notifications about the payment status.
    $request->notify_url = 'https://example.com/notification_url';
    
    // Define the URL where users will be redirected upon successful payment (optional).
    $request->return_url = 'https://example.com/success';
    
    // Set the URL to redirect users if they cancel the payment (optional).
    $request->cancel_url = 'https://example.com/cancel';
    
    // Provide a URL for the logo to be displayed on the payment page (optional).
    $request->logo_url = 'https://static.cdnlogo.com/logos/i/80/internet-society.svg';
    
    // Enable the display of available countries for payment on the checkout page.
    $request->showCountries = true;
    
    // Generate the HTML for the payment button with customization options.
    $buttonHtml = $AfribaPayButton->createCheckoutButton($request, 'Payer maintenant', '#FF5733', 'large');
    
    // Output the generated payment button HTML.
    echo $buttonHtml;

} catch (AfribaPayException $e) {
    echo "AfribaPayException: " . $e->getMessage();
} catch (Exception $e) {
    echo "UnknowException: " . $e->getMessage();
}