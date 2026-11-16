<?php
require_once dirname(__DIR__) . '/src/afribapay.sdk.php';
require_once __DIR__ . '/common.php';

try {
    $sdk = new AfribaPaySDK();
    
    $payments = [
        [
            'currency' => 'XOF',
            'country' => 'BF',
            'country_name' => 'Burkina Faso',
            'amount' => 5000,
            'description' => 'Payment in XOF (Burkina Faso)'
        ],
        [
            'currency' => 'XAF',
            'country' => 'CM',
            'country_name' => 'Cameroon',
            'amount' => 5000,
            'description' => 'Payment in XAF (Cameroon)'
        ],
        [
            'currency' => 'KES',
            'country' => 'KE',
            'country_name' => 'Kenya',
            'amount' => 500,
            'description' => 'Payment in KES (Kenya)'
        ],
        [
            'currency' => 'UGX',
            'country' => 'UG',
            'country_name' => 'Uganda',
            'amount' => 20000,
            'description' => 'Payment in UGX (Uganda)'
        ],
        [
            'currency' => 'RWF',
            'country' => 'RW',
            'country_name' => 'Rwanda',
            'amount' => 5000,
            'description' => 'Payment in RWF (Rwanda)'
        ],
        [
            'currency' => 'GNF',
            'country' => 'GN',
            'country_name' => 'Guinea',
            'amount' => 50000,
            'description' => 'Payment in GNF (Guinea)'
        ]
    ];
    
    echo getExampleHeader(
        'Example 8: Different Currencies',
        'Examples with all supported currencies across Africa.'
    );
    ?>
    
    <div class="alert alert-info border-start border-primary border-4">
        <h5 class="alert-heading"><i class="bi bi-currency-exchange"></i> Supported Currencies</h5>
        <ul class="mb-0">
            <li><strong>XOF</strong> - West African CFA Franc (BJ, BF, CI, GW, ML, NE, SN, TG)</li>
            <li><strong>XAF</strong> - Central African CFA Franc (CM, CF, TD, CG, GQ, GA)</li>
            <li><strong>CDF</strong> - Congolese Franc (CD)</li>
            <li><strong>GNF</strong> - Guinean Franc (GN)</li>
            <li><strong>KES</strong> - Kenyan Shilling (KE)</li>
            <li><strong>MWK</strong> - Malawian Kwacha (MW)</li>
            <li><strong>RWF</strong> - Rwandan Franc (RW)</li>
            <li><strong>SLE</strong> - Sierra Leonean Leone (SL)</li>
            <li><strong>UGX</strong> - Ugandan Shilling (UG)</li>
            <li><strong>ZMW</strong> - Zambian Kwacha (ZM)</li>
        </ul>
    </div>
    
    <div class="row mt-4">
        <?php foreach ($payments as $index => $payment): ?>
            <?php
            $request = new AfribaPayRequest();
            $request->description = $payment['description'];
            $request->amount = $payment['amount'];
            $request->currency = $payment['currency'];
            $request->country = $payment['country'];
            $request->order_id = 'ORDER-' . $payment['currency'] . '-' . time();
            $request->reference_id = 'REF-' . $payment['currency'] . '-' . uniqid();
            $request->company = 'My International Store';
            $request->checkout_name = 'Payment ' . $payment['currency'];
            $request->showCountries = true;
            
            $buttonHtml = $sdk->createCheckoutButton(
                $request,
                'Pay in ' . $payment['currency'],
                'primary',
                'medium'
            );
            ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($payment['country_name']); ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($payment['currency']); ?></span>
                        </h5>
                        <p class="card-text"><?php echo htmlspecialchars($payment['description']); ?></p>
                        <p class="h5">
                            <strong>Amount:</strong> 
                            <?php echo number_format($payment['amount'], 0, ',', ' '); ?> 
                            <?php echo htmlspecialchars($payment['currency']); ?>
                        </p>
                        <p><strong>Country Code:</strong> <?php echo htmlspecialchars($payment['country']); ?></p>
                        <div class="mt-3">
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
// XOF - Burkina Faso
$request->currency = "XOF";
$request->country = "BF";

// XAF - Cameroon
$request->currency = "XAF";
$request->country = "CM";

// KES - Kenya
$request->currency = "KES";
$request->country = "KE";
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
