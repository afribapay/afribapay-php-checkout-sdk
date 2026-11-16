<?php
require_once dirname(__DIR__) . '/src/afribapay.sdk.php';
require_once __DIR__ . '/common.php';

try {
    $sdk = new AfribaPaySDK();
    
    $request = new AfribaPayRequest();
    
    $request->amount = 10000;
    $request->currency = 'XOF';
    $request->country = 'BF';
    $request->order_id = 'ORDER-CALLBACK-' . time();
    $request->reference_id = 'REF-CALLBACK-' . uniqid();
    $request->description = 'Payment with callback handling';
    $request->company = 'My Store';
    $request->checkout_name = 'Secure Payment';
    
    // notify_url must be publicly reachable and accept POST
    // $request->notify_url = 'https://your-site.com/webhook/afribapay';
    // $request->return_url = 'https://your-site.com/payment/success?order_id=' . $request->order_id;
    // $request->cancel_url = 'https://your-site.com/payment/cancel?order_id=' . $request->order_id;
    
    $buttonHtml = $sdk->createCheckoutButton(
        $request,
        'Pay with Callbacks',
        'success',
        'large'
    );
    
    echo getExampleHeader(
        'Example 5: Callback URLs',
        'Configuration and usage of callback URLs for payment notifications and redirects.'
    );
    ?>
    
    <div class="alert alert-warning border-start border-warning border-4">
        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Important</h5>
        <p class="mb-0">These URLs must be publicly accessible and properly configured on your server to handle the callbacks.</p>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <h4 class="h5">Payment Button</h4>
            <div class="mt-3">
                <?php echo $buttonHtml; ?>
            </div>
        </div>
        <div class="col-md-6">
            <h4 class="h5">Configured URLs</h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Notify URL:</strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($request->notify_url ?? "https://mon-site.com/notify"); ?></small>
                    <br><span class="badge bg-info">Webhook for payment status</span>
                </li>
                <li class="list-group-item">
                    <strong>Return URL:</strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($request->return_url ?? "https://mon-site.com/success"); ?></small>
                    <br><span class="badge bg-success">After successful payment</span>
                </li>
                <li class="list-group-item">
                    <strong>Cancel URL:</strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($request->cancel_url ?? "https://mon-site.com/cancel"); ?></small>
                    <br><span class="badge bg-warning">If user cancels</span>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="mt-5">
        <h4 class="h5"><i class="bi bi-code-slash"></i> Code Example</h4>
        <div class="bg-light border-start border-primary border-4 p-3 rounded">
            <pre class="mb-0"><code><?php echo htmlspecialchars('
$request->notify_url = "https://your-site.com/webhook/afribapay";
$request->return_url = "https://your-site.com/payment/success?order_id=" . $request->order_id;
$request->cancel_url = "https://your-site.com/payment/cancel?order_id=" . $request->order_id;
'); ?></code></pre>
        </div>
    </div>
    
    <div class="mt-4">
        <h4 class="h5"><i class="bi bi-server"></i> Webhook Handler Example</h4>
        <div class="bg-light border-start border-primary border-4 p-3 rounded">
            <pre class="mb-0"><code><?php echo htmlspecialchars('
// Example webhook handler
// File: webhook/afribapay.php

$data = json_decode(base64_decode($_POST["afp_data"]), true);

// Check payment status
if ($data["status"] === "success") {
    // Update order in your database
    // order_id is available in $data["order_id"]
    updateOrderStatus($data["order_id"], "paid");
}
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
