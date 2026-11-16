<?php
require_once dirname(__DIR__) . '/src/afribapay.sdk.php';
require_once __DIR__ . '/common.php';

try {
    $sdk = new AfribaPaySDK();
    
    $request = new AfribaPayRequest();
    $request->description = 'Payment with custom CSS classes';
    $request->amount = 3000;
    $request->currency = 'XOF';
    $request->country = 'BF';
    $request->order_id = 'ORDER-CUSTOM-' . time();
    $request->reference_id = 'REF-CUSTOM-' . uniqid();
    $request->company = 'My Store';
    
    echo getExampleHeader(
        'Example 9: Custom CSS Classes',
        'Adding custom CSS classes to personalize button appearance.'
    );
    ?>
    
    <style>
        .btn-gradient {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: transform 0.2s;
        }
        
        .btn-gradient:hover {
            transform: scale(1.05);
        }
        
        .btn-shadow {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-rounded {
            border-radius: 25px !important;
        }
        
        .btn-animated {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
            100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
        }
    </style>
    
    <div class="alert alert-info border-start border-primary border-4">
        <h5 class="alert-heading"><i class="bi bi-paint-bucket"></i> Custom Styling</h5>
        <p class="mb-0">Add your own CSS classes to customize button appearance beyond Bootstrap defaults.</p>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <h4 class="h5">Button with Gradient</h4>
            <?php 
            $request->reference_id = 'REF-CUSTOM1-' . uniqid();
            echo $sdk->createCheckoutButton(
                $request,
                'Pay with Gradient',
                '#667eea',
                'large',
                ['btn-gradient', 'btn-shadow']
            );
            ?>
        </div>
        
        <div class="col-md-6 mb-4">
            <h4 class="h5">Button with Rounded Corners</h4>
            <?php 
            $request->reference_id = 'REF-CUSTOM2-' . uniqid();
            echo $sdk->createCheckoutButton(
                $request,
                'Pay (Rounded)',
                'success',
                'medium',
                ['btn-rounded']
            );
            ?>
        </div>
        
        <div class="col-md-6 mb-4">
            <h4 class="h5">Button with Animation</h4>
            <?php 
            $request->reference_id = 'REF-CUSTOM3-' . uniqid();
            echo $sdk->createCheckoutButton(
                $request,
                'Pay (Animated)',
                'danger',
                'large',
                ['btn-animated', 'btn-shadow']
            );
            ?>
        </div>
        
        <div class="col-md-6 mb-4">
            <h4 class="h5">Button with Multiple Classes</h4>
            <?php 
            $request->reference_id = 'REF-CUSTOM4-' . uniqid();
            echo $sdk->createCheckoutButton(
                $request,
                'Pay (Multi-classes)',
                '#1abc9c',
                'large',
                ['btn-gradient', 'btn-shadow', 'btn-rounded']
            );
            ?>
        </div>
    </div>
    
    <div class="mt-5">
        <h4 class="h5"><i class="bi bi-code-slash"></i> Code Example</h4>
        <div class="bg-light border-start border-primary border-4 p-3 rounded">
            <pre class="mb-0"><code><?php echo htmlspecialchars('
// Add custom CSS classes
$buttonHtml = $sdk->createCheckoutButton(
    $request,
    "Pay",
    "primary",
    "large",
    ["my-custom-class-1", "my-custom-class-2", "my-custom-class-3"]
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
