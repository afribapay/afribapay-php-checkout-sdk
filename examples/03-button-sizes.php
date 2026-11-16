<?php
require_once dirname(__DIR__) . '/src/afribapay.sdk.php';
require_once __DIR__ . '/common.php';

try {
    $sdk = new AfribaPaySDK();
    
    $request = new AfribaPayRequest();
    $request->description = 'Test payment';
    $request->amount = 1000;
    $request->currency = 'XOF';
    $request->country = 'BF';
    $request->order_id = 'ORDER-SIZE-' . time();
    $request->company = 'My Store';
    
    echo getExampleHeader(
        'Example 3: Button Sizes',
        'Demonstration of the three available button sizes: small, medium, and large.'
    );
    ?>
    
    <div class="alert alert-info border-start border-primary border-4">
        <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Available Sizes</h5>
        <ul class="mb-0">
            <li><strong>small</strong> or <strong>sm</strong> - Small button</li>
            <li><strong>medium</strong> or <strong>md</strong> - Medium button (default)</li>
            <li><strong>large</strong> or <strong>lg</strong> - Large button</li>
        </ul>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <h4 class="h5">Small</h4>
            <?php 
            $request->reference_id = 'REF-SMALL-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', '#28a745', 'small');
            ?>
        </div>
        
        <div class="col-md-4 mb-4">
            <h4 class="h5">Medium (Default)</h4>
            <?php 
            $request->reference_id = 'REF-MEDIUM-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', '#007bff', 'medium');
            ?>
        </div>
        
        <div class="col-md-4 mb-4">
            <h4 class="h5">Large</h4>
            <?php 
            $request->reference_id = 'REF-LARGE-' . uniqid();
            echo $sdk->createCheckoutButton($request, 'Pay', '#dc3545', 'large');
            ?>
        </div>
    </div>
    
    <div class="mt-4">
        <h4 class="h5">With Aliases (sm, md, lg)</h4>
        <p>You can also use aliases: sm, md, lg</p>
        <?php 
        $request->reference_id = 'REF-ALIAS-' . uniqid();
        echo $sdk->createCheckoutButton($request, 'Pay with Alias', '#6c757d', 'lg');
        ?>
    </div>
    
    <div class="mt-5">
        <h4 class="h5"><i class="bi bi-code-slash"></i> Code Example</h4>
        <div class="bg-light border-start border-primary border-4 p-3 rounded">
            <pre class="mb-0"><code><?php echo htmlspecialchars('
// Small button
$sdk->createCheckoutButton($request, "Pay", "#28a745", "small");

// Medium button (default)
$sdk->createCheckoutButton($request, "Pay", "#007bff", "medium");

// Large button
$sdk->createCheckoutButton($request, "Pay", "#dc3545", "large");

// Using aliases
$sdk->createCheckoutButton($request, "Pay", "#6c757d", "sm");
$sdk->createCheckoutButton($request, "Pay", "#6c757d", "md");
$sdk->createCheckoutButton($request, "Pay", "#6c757d", "lg");
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
