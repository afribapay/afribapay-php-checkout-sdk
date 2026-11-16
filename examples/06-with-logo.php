<?php
require_once dirname(__DIR__) . '/src/afribapay.sdk.php';
require_once __DIR__ . '/common.php';

try {
    $sdk = new AfribaPaySDK();
    
    $request = new AfribaPayRequest();
    
    $request->amount = 5000;
    $request->currency = 'XOF';
    $request->country = 'BF';
    $request->order_id = 'ORDER-LOGO-' . time();
    $request->reference_id = 'REF-LOGO-' . uniqid();
    $request->description = 'Payment with custom logo';
    $request->company = 'TechStore Africa';
    $request->checkout_name = 'TechStore - Payment';
    
    // Public image URL: JPEG, PNG, GIF, BMP, WebP or SVG
    $request->logo_url = 'https://static.cdnlogo.com/logos/i/80/internet-society.svg';
    // $request->logo_url = 'https://your-site.com/images/logo.png';
    
    $buttonHtml = $sdk->createCheckoutButton(
        $request,
        'Pay with Logo',
        'primary',
        'large'
    );
    
    echo getExampleHeader(
        'Example 6: Custom Logo',
        'How to add a custom logo to the checkout page.'
    );
    ?>
    
    <div class="alert alert-warning border-start border-warning border-4">
        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Important Requirements</h5>
        <ul class="mb-0">
            <li>The logo must be a valid URL and publicly accessible</li>
            <li>Supported formats: JPEG, PNG, GIF, BMP, WebP, SVG</li>
            <li>The SDK automatically validates that the URL points to a valid image</li>
            <li>If the URL is not valid, an exception will be thrown</li>
        </ul>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <h4 class="h5">Payment Button</h4>
            <div class="mt-3">
                <?php echo $buttonHtml; ?>
            </div>
        </div>
        <div class="col-md-6">
            <h4 class="h5">Logo Preview</h4>
            <div class="mt-3">
                <p><strong>Logo URL:</strong></p>
                <p><small class="text-muted"><?php echo htmlspecialchars($request->logo_url); ?></small></p>
                <img src="<?php echo htmlspecialchars($request->logo_url); ?>" 
                     alt="Logo" 
                     class="img-fluid" 
                     style="max-width: 200px; height: auto; border: 1px solid #dee2e6; padding: 10px; border-radius: 5px;">
            </div>
        </div>
    </div>
    
    <div class="mt-5">
        <h4 class="h5"><i class="bi bi-code-slash"></i> Code Example</h4>
        <div class="bg-light border-start border-primary border-4 p-3 rounded">
            <pre class="mb-0"><code><?php echo htmlspecialchars('
// PNG logo
$request->logo_url = "https://your-site.com/logo.png";

// JPEG logo
$request->logo_url = "https://your-site.com/logo.jpg";

// SVG logo
$request->logo_url = "https://your-site.com/logo.svg";

// WebP logo
$request->logo_url = "https://your-site.com/logo.webp";
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
