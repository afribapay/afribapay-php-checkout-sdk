<?php
require_once dirname(__DIR__) . '/src/afribapay.sdk.php';
require_once __DIR__ . '/common.php';

try {
    $sdk = new AfribaPaySDK();
    
    $request = new AfribaPayRequest();
    $request->description = 'Test payment';
    $request->amount = 2000;
    $request->currency = 'XOF';
    $request->country = 'BF';
    $request->order_id = 'ORDER-COLOR-' . time();
    $request->company = 'My Store';
    
    echo getExampleHeader(
        'Example 4: Button Colors',
        'Demonstration of Bootstrap predefined colors and custom hexadecimal colors.'
    );
    ?>
    
    <div class="alert alert-info border-start border-primary border-4">
        <h5 class="alert-heading"><i class="bi bi-palette"></i> Color Options</h5>
        <p class="mb-0">You can use Bootstrap predefined color names or any hexadecimal color code for custom styling.</p>
    </div>
    
    <h3 class="mt-4">Bootstrap Predefined Colors</h3>
    <div class="row mt-3">
        <div class="col-md-6 mb-3">
            <h5>Success (Green)</h5>
            <?php 
            $request->reference_id = 'REF-SUCCESS-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', 'success');
            ?>
        </div>
        
        <div class="col-md-6 mb-3">
            <h5>Primary (Blue)</h5>
            <?php 
            $request->reference_id = 'REF-PRIMARY-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', 'primary');
            ?>
        </div>
        
        <div class="col-md-6 mb-3">
            <h5>Danger (Red)</h5>
            <?php 
            $request->reference_id = 'REF-DANGER-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', 'danger');
            ?>
        </div>
        
        <div class="col-md-6 mb-3">
            <h5>Warning (Yellow)</h5>
            <?php 
            $request->reference_id = 'REF-WARNING-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', 'warning');
            ?>
        </div>
        
        <div class="col-md-6 mb-3">
            <h5>Info (Cyan)</h5>
            <?php 
            $request->reference_id = 'REF-INFO-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', 'info');
            ?>
        </div>
        
        <div class="col-md-6 mb-3">
            <h5>Dark (Black)</h5>
            <?php 
            $request->reference_id = 'REF-DARK-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', 'dark');
            ?>
        </div>
    </div>
    
    <h3 class="mt-5">Custom Colors (Hexadecimal)</h3>
    <div class="row mt-3">
        <div class="col-md-4 mb-3">
            <h5>Custom Color #FF5733 (Orange)</h5>
            <?php 
            $request->reference_id = 'REF-CUSTOM1-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', '#FF5733');
            ?>
        </div>
        
        <div class="col-md-4 mb-3">
            <h5>Custom Color #9B59B6 (Purple)</h5>
            <?php 
            $request->reference_id = 'REF-CUSTOM2-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', '#9B59B6');
            ?>
        </div>
        
        <div class="col-md-4 mb-3">
            <h5>Custom Color #1ABC9C (Turquoise)</h5>
            <?php 
            $request->reference_id = 'REF-CUSTOM3-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', '#1ABC9C');
            ?>
        </div>
    </div>
    
    <div class="mt-5">
        <h4 class="h5"><i class="bi bi-code-slash"></i> Code Example</h4>
        <div class="bg-light border-start border-primary border-4 p-3 rounded">
            <pre class="mb-0"><code><?php echo htmlspecialchars('
// Bootstrap predefined colors
$sdk->createCheckoutButton($request, "Pay", "success");
$sdk->createCheckoutButton($request, "Pay", "primary");
$sdk->createCheckoutButton($request, "Pay", "danger");
$sdk->createCheckoutButton($request, "Pay", "warning");
$sdk->createCheckoutButton($request, "Pay", "info");
$sdk->createCheckoutButton($request, "Pay", "dark");

// Custom hexadecimal colors
$sdk->createCheckoutButton($request, "Pay", "#FF5733");
$sdk->createCheckoutButton($request, "Pay", "#9B59B6");
$sdk->createCheckoutButton($request, "Pay", "#1ABC9C");
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
