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
    function createButton($amount, $currency, $description, $order_id, $reference_id, $country, $company, $checkout_name) {
        // Instantiate the AfribaPay SDK class with required configuration parameters
        $AfribaPayButton = new AfribaPaySDKClass(
            apiUser: 'pk_15fb8ccc-e2a8-4350-afad-acbf224f2e64', // Public API user identifier
            apiKey: 'sk_NA24xhNko7N96XJQZzBd337W33l5Ff5q4jSv1907m', // Secret API key for authentication
            agent_id: 'APM31923613', // Unique agent identifier
            merchantKey: 'mk_Dv2c9Us240920061620', // Merchant-specific key for transactions
            environment: 'sandbox', // Operating environment ('sandbox' for testing, 'production' for live transactions)
            lang: 'fr' // Language for the SDK interface (French)
        );
    
        // Create a new payment request with the provided details
        $request = new PaymentRequest();
    
        // Set the payment details
        $request->amount = $amount; // Payment amount
        $request->currency = $currency; // Payment currency
        $request->country = $country; // Country code for the transaction (e.g., 'BF' for Burkina Faso)       
        $request->order_id = $order_id; // Unique order ID for tracking
        $request->reference_id = $reference_id; // Unique reference ID for the transaction      
        $request->company = $company; // Company name initiating the transaction
        $request->checkout_name = $checkout_name; // Name displayed during checkout
        $request->description = $description; // Description of the transaction
        $request->logo_url = 'https://static.cdnlogo.com/logos/i/80/internet-society.svg'; // Logo URL for the payment page
        $request->notify_url = 'https://example.com/notification_url'; // URL for payment notifications
        $request->return_url = 'https://example.com/success'; // URL to redirect upon successful payment
        $request->cancel_url = 'https://example.com/cancel'; // URL to redirect if payment is canceled
        $request->showCountries = true; // Display available countries during checkout
    
        // Generate and return the AfribaPay checkout button HTML
        return $AfribaPayButton->createCheckoutButton($request, 'Payer maintenant', '#7973FF', 'large');
    }
    
try {

    // Call the function with payment details and echo the generated button HTML
    echo createButton(
        amount: 75000, // Payment amount in the specified currency
        currency: 'XOF', // Currency code (e.g., West African CFA franc)
        country: 'BF', // Country code (e.g., 'BF' for Burkina Faso)
        order_id: 'ORDER123', // Unique order ID
        reference_id: 'ref-tfp-bf', // Reference ID for the transaction
        checkout_name: 'Voiture', // Checkout name displayed on the payment page
        company: 'WIKI BI Test', // Name of the company initiating the transaction        
        description: 'Changan car payment' // Description of the transaction
    );

} catch (AfribaPayException $e) {
    echo "AfribaPayException: " . $e->getMessage();
} catch (Exception $e) {
    echo "UnknowException: " . $e->getMessage();
}