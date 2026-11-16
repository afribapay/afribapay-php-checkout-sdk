<?php
require_once dirname(__DIR__) . '/src/afribapay.sdk.php';
require_once __DIR__ . '/common.php';

try {
    $sdk = new AfribaPaySDK();
    
    $request = new AfribaPayRequest();
    
    $request->amount = 15000;
    $request->currency = 'XOF';
    $request->country = 'BF';
    $request->order_id = 'ORDER-' . time();
    $request->reference_id = 'REF-' . uniqid();
    
    $request->description = 'Purchase of electronic products';
    $request->company = 'TechStore Africa';
    $request->checkout_name = 'TechStore - Secure Payment';
    
    // Callback URLs (optional)
    // $request->notify_url = 'https://your-site.com/webhook/afribapay';
    // $request->return_url = 'https://your-site.com/payment/success';
    // $request->cancel_url = 'https://your-site.com/payment/cancel';
    
    // Logo (optional): public image URL
    // $request->logo_url = 'https://your-site.com/logo.png';
    
    $request->showCountries = true;
    
    $buttonHtml = $sdk->createCheckoutButton(
        $request,
        'Pay Now',
        '#007bff',
        'large' // small, medium, large
    );
    
    echo getExampleHeader(
        'Example 2: Full Options',
        'Complete example using all available SDK options.'
    );
    ?>
    
    <div class="alert alert-info border-start border-primary border-4">
        <h5 class="alert-heading"><i class="bi bi-info-circle"></i> About this example</h5>
        <p>This example demonstrates all available options including callbacks, logo, and button customization.</p>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <h4 class="h5">Payment Button</h4>
            <div class="mt-3">
                <?php echo $buttonHtml; ?>
            </div>
        </div>
        <div class="col-md-6">
            <h4 class="h5">Configuration</h4>
            <ul class="list-group">
                <li class="list-group-item"><strong>Amount:</strong> <?php echo number_format($request->amount, 0, ',', ' '); ?> <?php echo htmlspecialchars($request->currency); ?></li>
                <li class="list-group-item"><strong>Country:</strong> <?php echo htmlspecialchars($request->country); ?></li>
                <li class="list-group-item"><strong>Company:</strong> <?php echo htmlspecialchars($request->company); ?></li>
                <li class="list-group-item"><strong>Show Countries:</strong> <?php echo $request->showCountries ? 'Yes' : 'No'; ?></li>
                <li class="list-group-item"><strong>Notify URL:</strong> <small><?php echo htmlspecialchars($request->notify_url ?? "https://mon-site.com/notify"); ?></small></li>
                <li class="list-group-item"><strong>Return URL:</strong> <small><?php echo htmlspecialchars($request->return_url ?? "https://mon-site.com/success"); ?></small></li>
                <li class="list-group-item"><strong>Cancel URL:</strong> <small><?php echo htmlspecialchars($request->cancel_url ?? "https://mon-site.com/cancel"); ?></small></li>
            </ul>
        </div>
    </div>
    
    <div class="mt-5">
        <h4 class="h5"><i class="bi bi-code-slash"></i> Code Example</h4>
        <div class="bg-light border-start border-primary border-4 p-3 rounded">
            <pre class="mb-0"><code><?php echo htmlspecialchars('
$request = new AfribaPayRequest();
$request->amount = 15000;
$request->currency = "XOF";
$request->country = "BF";
$request->order_id = "ORDER-" . time();
$request->reference_id = "REF-" . uniqid();
$request->description = "Purchase of electronic products";
$request->company = "TechStore Africa";
$request->checkout_name = "TechStore - Secure Payment";

// Callback URLs
$request->notify_url = "https://your-site.com/webhook/afribapay";
$request->return_url = "https://your-site.com/payment/success";
$request->cancel_url = "https://your-site.com/payment/cancel";

// Logo (optional)
// $request->logo_url = "https://your-site.com/logo.png";

$request->showCountries = true;

$buttonHtml = $sdk->createCheckoutButton(
    $request,
    "Pay Now",
    "#007bff",
    "large"
);
'); ?></code></pre>
        </div>
    </div>
    
    <?php
    echo getExampleFooter();
    
} catch (AfribaPayException $e) {
    echo getExampleHeader('Error');
    echo '<div class="alert alert-danger">AfribaPay Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo getExampleFooter();
} catch (Exception $e) {
    echo getExampleHeader('Error');
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo getExampleFooter();
}
