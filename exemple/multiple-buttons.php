<?php
/**
 ***
 *** Refer to https://docs.afribapay.com for extra documentation
 ***
 **/
$sdkPath = dirname(__FILE__,2).'/src/afribapay.sdk.php';
if (file_exists($sdkPath)) {
    require_once($sdkPath);
} else {
    die("The SDK file was not found at the specified location : " . $sdkPath);
}
// Produits en ventes
$products = [
    [
        'id' => 1,
        'item_name' => "Smartphone UltraFast X1",
        'price' => 450000,
        'currency' => "USD",
        'reference_id' => "SN-X1-0001",
        'image' => "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
        'description' => "Le Smartphone UltraFast X1 est l'appareil le plus avancé de notre gamme. Avec son processeur octa-core et son écran AMOLED de 6,5 pouces, il offre une expérience utilisateur incomparable."
    ],
    [
        'id' => 2,
        'item_name' => "Laptop ProBook Z3",
        'price' => 750000,
        'currency' => "GNF",
        'reference_id' => "SN-Z3-0002",
        'image' => "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
        'description' => "Le Laptop ProBook Z3 est conçu pour les professionnels exigeants. Avec son processeur de dernière génération et son écran 4K, il vous accompagne dans tous vos projets."
    ],
    [
        'id' => 3,
        'item_name' => "Tablette SlimTab T2",
        'price' => 250000,
        'currency' => "XOF",
        'reference_id' => "SN-T2-0003",
        'image' => "https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
        'description' => "La Tablette SlimTab T2 allie légèreté et puissance. Parfaite pour le divertissement et la productivité en déplacement."
    ],
    [
        'id' => 4,
        'item_name' => "Écouteurs sans fil SoundPro",
        'price' => 80000,
        'currency' => "XOF",
        'reference_id' => "SN-SP-0004",
        'image' => "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
        'description' => "Les Écouteurs sans fil SoundPro offrent une qualité audio exceptionnelle et une autonomie de 24 heures. Idéals pour les mélomanes en mouvement."
    ],
    [
        'id' => 5,
        'item_name' => "Montre connectée FitTrack",
        'price' => 120000,
        'currency' => "XOF",
        'reference_id' => "SN-FT-0005",
        'image' => "https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
        'description' => "La Montre connectée FitTrack surveille votre santé et vos performances sportives. Son design élégant en fait un accessoire parfait pour toutes les occasions."
    ],
    [
        'id' => 6,
        'item_name' => "Caméra d'action AdventurePro",
        'price' => 180000,
        'currency' => "XOF",
        'reference_id' => "SN-AP-0006",
        'image' => "https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
        'description' => "La Caméra d'action AdventurePro capture vos aventures en 4K. Résistante à l'eau et aux chocs, elle vous suit partout."
    ]
];
// Gérer la navigation
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
// Fonction pour afficher les produits

function displayProducts($products) {
    // Instantiate AfribaPay SDK with required configuration parameters
    $AfribaPayButton = new AfribaPaySDKClass(
        apiUser: 'pk_15fb8ccc-e2a8-4350-afad-acbf224f2e64', // API user identifier
        apiKey: 'sk_NA24xhNko7N96XJQZzBd337W33l5Ff5q4jSv1907m', // Secret API key for authentication
        agent_id: 'APM31923613', // Unique identifier for the agent
        merchantKey: 'mk_Dv2c9Us240920061620', // Merchant-specific key for transactions
        environment: 'sandbox', // (production, or sandbox)
        lang: 'fr' // Language for the SDK interface (French in this case)
    );

    // Iterate over the list of products to generate payment buttons for each
    foreach ($products as $product) {
        // Prepare the payment request with product-specific details
        $request = new PaymentRequest();        
        $request->amount = $product['price']; // Amount to be paid for the product
        $request->currency = $product['currency']; // Currency for the payment        
        $request->order_id = $product['id']; // Unique order ID for the product
        $request->reference_id = $product['reference_id']; // Reference ID, such as a serial number
        $request->checkout_name = $product['item_name']; // Name of the product for the checkout interface
        $request->company = 'TechShop Inc'; // Name of the company initiating the transaction
        $request->description = $product['description']; // Description of the product
        $request->notify_url = 'https://example.com/notification_url'; // URL for payment notifications
        $request->return_url = 'https://example.com/success'; // URL to redirect upon successful payment
        $request->cancel_url = 'https://example.com/cancel'; // URL to redirect if payment is canceled
        $request->showCountries = true; // Enable display of countries during checkout
        $request->logo_url = 'https://static.cdnlogo.com/logos/i/80/internet-society.svg'; // Logo URL for the checkout page

        // Generate the AfribaPay payment button
        $buttonHtml = $AfribaPayButton->createCheckoutButton($request, 'Acheter', '#3498DB', 'medium', ['btn']);

        // Render the product card with visual elements
        echo '<div class="product-card">';
        echo '<img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['item_name']) . '">'; // Product image
        echo '<h3>' . htmlspecialchars($product['item_name']) . '</h3>'; // Product name
        echo '<p class="price">' . number_format($product['price'], 2, ',', ' ') . currency($product['currency']). ' </p>'; // Formatted price with currency
        echo '<p class="description">' . htmlspecialchars($product['description']) . '</p>'; // Product description
        echo $buttonHtml; // Payment button HTML
        echo '</div>';
    }
}

// fonction pour formater l'affichage de currency
function currency($currency){
    switch($currency){
        case 'XOF': return ' F CFA';
        case 'USD': return ' $';
        case 'EURO': return ' €';
        default: return ' '.strtoupper($currency);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Votre boutique en ligne</title>
    <link rel="icon" href="./logo.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #3498db;
            color: white;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center; 
        }
        header img {
            max-width: 150px; 
            height: auto;
            margin-right: 15px;
        }
        header h1 {
            margin: 0; 
            font-size: 1.8rem; 
        }
        nav {
            background-color: #2980b9;
            padding: 0.5rem;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            margin: 0 10px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        nav ul li a:hover {
            color: #ecf0f1;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .product-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
        .product-card h3 {
            margin: 10px 0;
            color: #2c3e50;
        }
        .product-card .price {
            font-weight: bold;
            color: #e74c3c;
            font-size: 1.2em;
        }
        .product-card .description {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 3px;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        footer {
            background-color: #34495e;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 20px;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .cart-table th, .cart-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .cart-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <header>
        <img src="./logo.png" alt="">
        <h1>TechShop</h1>
    </header>
    <nav>
        <ul>
            <li><a href="?page=home">Accueil</a></li>
            <li><a href="?page=products">Produits</a></li>
            <li><a href="?page=about">À propos</a></li>
            <li><a href="?page=contact">Contact</a></li>
        </ul>
    </nav>
    <div class="container">
        <?php
        switch($page) {
            case 'home':
                echo '<h2>Bienvenue sur TechShop</h2>';
                echo '<p>Découvrez nos derniers produits technologiques à des price imbattables !</p>';
                echo '<h3>Produits en vedette</h3>';
                echo '<div class="product-grid">';
                displayProducts(array_slice($products, 0, 3));
                echo '</div>';
                break;
            case 'products':
                echo '<h2>Nos Produits</h2>';
                echo '<div class="product-grid">';
                displayProducts($products);
                echo '</div>';
                break;
            case 'about':
                echo '<h2>À propos de TechShop</h2>';
                echo '<p>TechShop est votre destination en ligne pour tous vos besoins en technologie. Fondée en 2010, notre entreprise s\'engage à offrir les derniers gadgets et appareils électroniques à des price compétitifs.</p>';
                echo '<p>Notre mission est de rendre la technologie accessible à tous. Nous sélectionnons soigneusement chaque produit pour garantir qualité et innovation à nos clients.</p>';
                echo '<p>Chez TechShop, nous croyons en :</p>';
                echo '<ul>';
                echo '<li>L\'excellence du service client</li>';
                echo '<li>La qualité et la fiabilité des produits</li>';
                echo '<li>L\'innovation continue</li>';
                echo '<li>La satisfaction garantie</li>';
                echo '</ul>';
                break;
            case 'contact':
                echo '<h2>Contactez-nous</h2>';
                echo '<p>Nous sommes là pour répondre à toutes vos questions. N\'hésitez pas à nous contacter :</p>';
                echo '<ul>';
                echo '<li>Email : contact@techshop.com</li>';
                echo '<li>Téléphone : 01 23 45 67 89</li>';
                echo '<li>Adresse : 123 Rue de la Technologie, 75000 Paris</li>';
                echo '</ul>';
                echo '<h3>Horaires d\'ouverture :</h3>';
                echo '<p>Du lundi au vendredi : 9h - 18h<br>Samedi : 10h - 16h<br>Dimanche : Fermé</p>';
                echo '<h3>Service client :</h3>';
                echo '<p>Notre équipe de support est disponible pour vous aider avec :</p>';
                echo '<ul>';
                echo '<li>Questions sur les produits</li>';
                echo '<li>Suivi de commande</li>';
                echo '<li>Retours et échanges</li>';
                echo '<li>Assistance technique</li>';
                echo '</ul>';
                break;
            default:
                echo '<h2>Page non trouvée</h2>';
                echo '<p>Désolé, la page que vous recherchez n\'existe pas.</p>';
        }
        ?>
    </div>
    <footer>
        <p>&copy; 2024 TechShop. Tous droits réservés.</p>
    </footer>
</body>
</html>