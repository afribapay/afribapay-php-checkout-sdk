<?php
require_once dirname(__DIR__) . '/src/afribapay.sdk.php';
require_once __DIR__ . '/common.php';

try {
    $sdk = new AfribaPaySDK();
    
    $products = [
        [
            'name' => 'Premium Smartphone',
            'price' => 150000,
            'currency' => 'XOF',
            'country' => 'BF',
            'description' => 'High-end smartphone with AMOLED display'
        ],
        [
            'name' => 'Laptop Computer',
            'price' => 450000,
            'currency' => 'XOF',
            'country' => 'BF',
            'description' => 'Professional 15-inch laptop'
        ],
        [
            'name' => 'Tablet',
            'price' => 80000,
            'currency' => 'XOF',
            'country' => 'BF',
            'description' => '10-inch tablet with stylus'
        ],
        [
            'name' => 'Bluetooth Headphones',
            'price' => 25000,
            'currency' => 'XOF',
            'country' => 'BF',
            'description' => 'Wireless headphones with noise cancellation'
        ]
    ];
    
    echo getExampleHeader(
        'Example 7: Multiple Payment Buttons',
        'Creating multiple payment buttons on a single page (e.g., product catalog).'
    );
    ?>
    
    <div class="alert alert-info border-start border-primary border-4">
        <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Important Note</h5>
        <p class="mb-0">The JavaScript script is loaded only once, even with multiple buttons. Each button has its own form with its own payment data.</p>
    </div>
    
    <h3 class="mt-4">Product Catalog</h3>
    <div class="row mt-4">
        <?php foreach ($products as $index => $product): ?>
            <?php
            $request = new AfribaPayRequest();
            $request->description = $product['description'];
            $request->amount = $product['price'];
            $request->currency = $product['currency'];
            $request->country = $product['country'];
            $request->order_id = 'ORDER-PROD-' . ($index + 1) . '-' . time();
            $request->reference_id = 'REF-PROD-' . ($index + 1) . '-' . uniqid();
            $request->company = 'TechStore Africa';
            $request->checkout_name = $product['name'];
            $request->showCountries = true;
            
            $colors = ['primary', 'success', 'info', 'warning'];
            $buttonHtml = $sdk->createCheckoutButton(
                $request,
                'Buy ' . $product['name'],
                $colors[$index % count($colors)],
                'medium'
            );
            ?>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text flex-grow-1"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="h4 text-primary mb-3">
                            <?php echo number_format($product['price'], 0, ',', ' '); ?> 
                            <?php echo htmlspecialchars($product['currency']); ?>
                        </p>
                        <div class="mt-auto">
                            <?php echo $buttonHtml; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-5">
        <h4 class="h5"><i class="bi bi-code-slash"></i> Code Example</h4>
        <div class="bg-light border-start border-primary border-4 p-3 rounded">
            <pre class="mb-0"><code><?php echo htmlspecialchars('
$products = [
    ["name" => "Premium Smartphone", "price" => 150000, ...],
    ["name" => "Laptop Computer", "price" => 450000, ...],
    // ...
];

foreach ($products as $index => $product) {
    $request = new AfribaPayRequest();
    $request->description = $product["description"];
    $request->amount = $product["price"];
    // ... configure request
    
    $buttonHtml = $sdk->createCheckoutButton($request, "Buy", "primary", "medium");
    echo $buttonHtml;
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
