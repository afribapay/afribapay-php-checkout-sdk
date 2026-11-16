<?php
require_once dirname(__DIR__) . '/src/afribapay.sdk.php';
require_once __DIR__ . '/common.php';

try {
    $sdk = new AfribaPaySDK();
    
    $request = new AfribaPayRequest();
    
    // Required
    $request->description = 'Payment for order #12345';
    
    // Optional but recommended
    $request->amount = 5000;
    $request->currency = 'XOF'; // Currency (XOF, XAF, CDF, GNF, KES, MWK, RWF, SLE, UGX, ZMW)
    $request->country = 'BF';
    $request->order_id = 'ORDER-' . time();
    $request->reference_id = 'REF-' . uniqid();
    
    $request->company = 'My Store';
    $request->checkout_name = 'My Store - Payment';
    
    $buttonHtml = $sdk->createCheckoutButton($request);
    
    echo getExampleHeader(
        'Example 1: Basic Payment',
        'A simple payment example with essential parameters only.'
    );
    ?>
    
    <div class="alert alert-info border-start border-primary border-4">
        <h5 class="alert-heading"><i class="bi bi-info-circle"></i> About this example</h5>
        <p class="mb-0">This example demonstrates a basic payment setup with the minimum required and recommended parameters.</p>
    </div>
    
    <div class="mt-4">
        <h4 class="h5">Payment Button</h4>
        <div class="d-inline-block mt-2">
            <?php echo $buttonHtml; ?>
        </div>
    </div>
    
    <div class="mt-5">
        <h4 class="h5"><i class="bi bi-code-slash"></i> Code Example</h4>
        <div class="bg-light border-start border-primary border-4 p-3 rounded">
            <pre class="mb-0"><code><?php echo htmlspecialchars('
$sdk = new AfribaPaySDK();

$request = new AfribaPayRequest();
$request->description = "Payment for order #12345";
$request->amount = 5000;
$request->currency = "XOF";
$request->country = "BF";
$request->order_id = "ORDER-" . time();
$request->reference_id = "REF-" . uniqid();
$request->company = "My Store";
$request->checkout_name = "My Store - Payment";

$buttonHtml = $sdk->createCheckoutButton($request);
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
