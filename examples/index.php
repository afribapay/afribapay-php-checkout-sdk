<?php
require_once __DIR__ . '/common.php';
echo getDocumentHead('Web Checkout | AfribaPay');
echo getExampleHeader(
    'AfribaPay SDK Examples',
    'Discover all possible use cases with practical examples',
    false
);
$examples_url = getExampleRelativePath();
$examples = [
    [
        'number' => '01',
        'title' => 'Basic Payment',
        'description' => 'Simple example with essential parameters only. Perfect for getting started quickly.',
        'icon' => 'bi-cash-coin',
        'features' => ['Essential parameters', 'Quick setup'],
        'url' => $examples_url . '/01-basic-payment',
        'color' => 'primary',
        'category' => 'Getting Started',
        'popular' => true
    ],
    [
        'number' => '02',
        'title' => 'Full Options',
        'description' => 'Complete usage of all available features. Advanced configuration with all options.',
        'icon' => 'bi-sliders',
        'features' => ['All features', 'Advanced config'],
        'url' => $examples_url . '/02-full-options',
        'color' => 'success',
        'category' => 'Advanced',
        'popular' => false
    ],
    [
        'number' => '03',
        'title' => 'Button Sizes',
        'description' => 'Demonstration of three sizes: small, medium, and large buttons.',
        'icon' => 'bi-arrows-expand',
        'features' => ['Small, Medium, Large', 'Size customization'],
        'url' => $examples_url . '/03-button-sizes',
        'color' => 'info',
        'category' => 'Customization',
        'popular' => false
    ],
    [
        'number' => '04',
        'title' => 'Button Colors',
        'description' => 'Bootstrap predefined colors and custom hexadecimal color codes.',
        'icon' => 'bi-palette',
        'features' => ['Bootstrap colors', 'Custom hex colors'],
        'url' => $examples_url . '/04-button-colors',
        'color' => 'warning',
        'category' => 'Customization',
        'popular' => true
    ],
    [
        'number' => '05',
        'title' => 'Callback URLs',
        'description' => 'Configuration of webhooks and redirect URLs for payment notifications.',
        'icon' => 'bi-webhook',
        'features' => ['Webhook setup', 'Redirect URLs'],
        'url' => $examples_url . '/05-callbacks',
        'color' => 'danger',
        'category' => 'Integration',
        'popular' => false
    ],
    [
        'number' => '06',
        'title' => 'Custom Logo',
        'description' => 'Adding a custom logo to the checkout page for brand personalization.',
        'icon' => 'bi-image',
        'features' => ['Logo upload', 'Brand customization'],
        'url' => $examples_url . '/06-with-logo',
        'color' => 'primary',
        'category' => 'Branding',
        'popular' => false
    ],
    [
        'number' => '07',
        'title' => 'Multiple Buttons',
        'description' => 'Creating multiple payment buttons on a single page for product catalogs.',
        'icon' => 'bi-grid-3x3-gap',
        'features' => ['Multiple buttons', 'Catalog style'],
        'url' => $examples_url . '/07-multiple-buttons',
        'color' => 'success',
        'category' => 'E-commerce',
        'popular' => true
    ],
    [
        'number' => '08',
        'title' => 'Different Currencies',
        'description' => 'Examples with all supported currencies across Africa (XOF, XAF, KES, etc.).',
        'icon' => 'bi-currency-exchange',
        'features' => ['10+ currencies', 'Multi-country'],
        'url' => $examples_url . '/08-different-currencies',
        'color' => 'info',
        'category' => 'International',
        'popular' => false
    ],
    [
        'number' => '09',
        'title' => 'Custom CSS Classes',
        'description' => 'Adding custom CSS classes to personalize button appearance and styling.',
        'icon' => 'bi-paint-bucket',
        'features' => ['Custom styling', 'CSS classes'],
        'url' => $examples_url . '/09-custom-classes',
        'color' => 'warning',
        'category' => 'Customization',
        'popular' => false
    ],
    [
        'number' => '10',
        'title' => 'Minimal Payment',
        'description' => 'Minimal configuration example with only the required description field.',
        'icon' => 'bi-file-earmark-code',
        'features' => ['Minimal setup', 'Quick start'],
        'url' => $examples_url . '/10-minimal',
        'color' => 'danger',
        'category' => 'Getting Started',
        'popular' => false
    ]
];

$categories = array_unique(array_column($examples, 'category'));
sort($categories); 
?>

<div class="row mb-5">
    <div class="col-12">
        <div class="bg-primary bg-gradient text-white rounded-4 p-5 shadow-lg position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);">
            <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 15rem; line-height: 1; transform: rotate(15deg);">
                <i class="bi bi-code-square"></i>
            </div>
            <div class="position-absolute bottom-0 start-0 opacity-10" style="font-size: 10rem; line-height: 1; transform: rotate(-15deg);">
                <i class="bi bi-rocket-takeoff"></i>
            </div>
            <div class="position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="display-4 fw-bold mb-3">
                            <i class="bi bi-rocket-takeoff me-2"></i>
                            Get Started with Examples
                        </h2>
                        <p class="lead mb-4 opacity-90" style="font-size: 1.25rem;">
                            Explore our comprehensive collection of integration examples. Each example demonstrates specific features and use cases to help you implement AfribaPay quickly and efficiently.
                        </p>
                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <span class="badge bg-white bg-opacity-20 text-dark border border-white border-opacity-50 fw-normal">
                                <i class="bi bi-check-circle me-2"></i> 10 Examples
                            </span>
                            <span class="badge bg-white bg-opacity-20 text-dark border border-white border-opacity-50 fw-normal">
                                <i class="bi bi-lightning-charge me-2"></i> Ready to Use
                            </span>
                            <span class="badge bg-white bg-opacity-20 text-dark border border-white border-opacity-50 fw-normal">
                                <i class="bi bi-book me-2"></i> Well Documented
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center mt-4 mt-lg-0">
                        <div class="bg-white bg-opacity-10 rounded-4 p-4 border border-white border-opacity-20">
                            <i class="bi bi-stars text-warning" style="font-size: 4rem;"></i>
                            <p class="mt-3 mb-0 fw-semibold">Start Building Today</p>
                            <small class="opacity-75">Copy, paste, and customize</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-2">
                            <i class="bi bi-funnel me-2 text-primary"></i>
                            Filter by Category
                        </h5>
                        <p class="text-muted small mb-0">Click on a category to filter examples</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary btn-sm filter-btn active" data-category="all" 
                                onclick="filterExamples('all')">
                            <i class="bi bi-grid-3x3 me-1"></i> All
                        </button>
                        <?php foreach ($categories as $category): ?>
                            <button class="btn btn-outline-primary btn-sm filter-btn" 
                                    data-category="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $category))); ?>"
                                    onclick="filterExamples('<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $category))); ?>')">
                                <?php echo htmlspecialchars($category); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4" id="examples-grid">
    <?php foreach ($examples as $example): 
        $categorySlug = strtolower(str_replace(' ', '-', $example['category']));
    ?>
        <div class="col-md-6 col-lg-4 example-card" 
             data-category="<?php echo htmlspecialchars($categorySlug); ?>"
             style="transition: opacity 0.3s ease, transform 0.3s ease;">
            <div class="card h-100 shadow-sm border-0 overflow-hidden position-relative" 
                 style="transition: transform 0.3s ease, box-shadow 0.3s ease;"
                 onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 0.75rem 2rem rgba(0,0,0,0.15)';"
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0,0,0,0.075)';">
                
                <?php if ($example['popular']): ?>
                    <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill fw-bold shadow-sm" 
                          style="z-index: 10; font-size: 0.75rem;">
                        <i class="bi bi-star-fill me-1"></i> Popular
                    </span>
                <?php endif; ?>
                
                <div class="bg-<?php echo htmlspecialchars($example['color']); ?> bg-gradient d-flex align-items-center justify-content-center position-relative" 
                     style="height: 220px; background: linear-gradient(135deg, var(--bs-<?php echo htmlspecialchars($example['color']); ?>), var(--bs-<?php echo htmlspecialchars($example['color']); ?>-dark));">
                    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" 
                         style="background-image: radial-gradient(circle, rgba(255,255,255,0.4) 1px, transparent 1px); background-size: 25px 25px;"></div>
                    <i class="bi <?php echo htmlspecialchars($example['icon']); ?> text-white position-relative" 
                       style="font-size: 5.5rem; z-index: 1; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3)); transition: transform 0.3s ease;"
                       onmouseover="this.style.transform='scale(1.1) rotate(5deg)';"
                       onmouseout="this.style.transform='scale(1) rotate(0deg)';"></i>
                    <span class="badge bg-white text-<?php echo htmlspecialchars($example['color']); ?> position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill fw-bold shadow-lg" 
                          style="font-size: 0.875rem; z-index: 10;">
                        #<?php echo htmlspecialchars($example['number']); ?>
                    </span>
                </div>
                
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2">
                        <span class="badge bg-<?php echo htmlspecialchars($example['color']); ?> bg-opacity-10 text-<?php echo htmlspecialchars($example['color']); ?> px-3 py-1 rounded-pill" 
                              style="font-size: 0.75rem; font-weight: 500;">
                            <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($example['category']); ?>
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="card-title fw-bold mb-2 fs-5" style="line-height: 1.3;">
                            <?php echo htmlspecialchars($example['title']); ?>
                        </h5>
                        <p class="card-text text-muted mb-0 flex-grow-1" style="line-height: 1.7; min-height: 52px; font-size: 0.9rem;">
                            <?php echo htmlspecialchars($example['description']); ?>
                        </p>
                    </div>
                    
                    <div class="mb-3 flex-grow-1">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($example['features'] as $feature): ?>
                                <li class="small mb-2 d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-<?php echo htmlspecialchars($example['color']); ?> me-2 mt-1 flex-shrink-0" 
                                       style="font-size: 0.875rem;"></i>
                                    <span class="text-secondary" style="line-height: 1.5;"><?php echo htmlspecialchars($feature); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <a href="<?php echo htmlspecialchars($example['url'].'.php'); ?>" 
                       class="btn btn-<?php echo htmlspecialchars($example['color']); ?> w-100 mt-auto fw-semibold d-flex align-items-center justify-content-center gap-2 py-2"
                       style="transition: all 0.3s ease;"
                       onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.2)';"
                       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                        <span>View Example</span>
                        <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row mt-5 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-lg bg-gradient" 
             style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h4 class="fw-bold mb-2">
                        <i class="bi bi-graph-up-arrow text-primary me-2"></i>
                        Why Choose Our SDK?
                    </h4>
                    <p class="text-muted mb-0">Everything you need to integrate payments seamlessly</p>
                </div>
                <div class="row g-4 text-center">
                    <div class="col-6 col-md-3">
                        <div class="d-flex flex-column align-items-center p-3 rounded-3 bg-white shadow-sm h-100"
                             style="transition: transform 0.3s ease;"
                             onmouseover="this.style.transform='translateY(-5px)';"
                             onmouseout="this.style.transform='translateY(0)';">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 mb-3">
                                <i class="bi bi-collection text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h3 class="fw-bold mb-1 display-6">10</h3>
                            <p class="text-muted small mb-0 fw-semibold">Examples</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex flex-column align-items-center p-3 rounded-3 bg-white shadow-sm h-100"
                             style="transition: transform 0.3s ease;"
                             onmouseover="this.style.transform='translateY(-5px)';"
                             onmouseout="this.style.transform='translateY(0)';">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 mb-3">
                                <i class="bi bi-code-slash text-success" style="font-size: 2.5rem;"></i>
                            </div>
                            <h3 class="fw-bold mb-1 display-6">100%</h3>
                            <p class="text-muted small mb-0 fw-semibold">Bootstrap</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex flex-column align-items-center p-3 rounded-3 bg-white shadow-sm h-100"
                             style="transition: transform 0.3s ease;"
                             onmouseover="this.style.transform='translateY(-5px)';"
                             onmouseout="this.style.transform='translateY(0)';">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 mb-3">
                                <i class="bi bi-lightning-charge text-warning" style="font-size: 2.5rem;"></i>
                            </div>
                            <h3 class="fw-bold mb-1 display-6">Fast</h3>
                            <p class="text-muted small mb-0 fw-semibold">Integration</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex flex-column align-items-center p-3 rounded-3 bg-white shadow-sm h-100"
                             style="transition: transform 0.3s ease;"
                             onmouseover="this.style.transform='translateY(-5px)';"
                             onmouseout="this.style.transform='translateY(0)';">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 mb-3">
                                <i class="bi bi-shield-check text-danger" style="font-size: 2.5rem;"></i>
                            </div>
                            <h3 class="fw-bold mb-1 display-6">Safe</h3>
                            <p class="text-muted small mb-0 fw-semibold">Production Ready</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterExamples(category) {
    const cards = document.querySelectorAll('.example-card');
    const buttons = document.querySelectorAll('.filter-btn');
    
    buttons.forEach(btn => {
        if (btn.dataset.category === category) {
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary', 'active');
        } else {
            btn.classList.remove('btn-primary', 'active');
            btn.classList.add('btn-outline-primary');
        }
    });
    
    cards.forEach(card => {
        const cardCategory = card.dataset.category;
        if (category === 'all' || cardCategory === category) {
            card.style.display = 'block';
            card.style.opacity = '1';
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 10);
        } else {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });
    
    const visibleCount = category === 'all' ? cards.length : document.querySelectorAll(`.example-card[data-category="${category}"]`).length;
}
</script>

<?= getExampleFooter(); ?>
<?= getDocumentFooter(); ?>
