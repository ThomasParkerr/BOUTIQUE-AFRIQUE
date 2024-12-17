<?php
session_start();
// Database connection
require_once "../db/database.php";
 // Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Fetch trending products
$trendingQuery = "SELECT * FROM AfriqueBotique_Products WHERE product_type = 'Men' LIMIT 5";
$stmt = $pdo->prepare($trendingQuery);
$stmt->execute();
$trendingProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$CategoriesQuery = "SELECT * FROM AfriqueBotique_Products WHERE product_type = 'Women' LIMIT 6";
$stmt = $pdo->prepare($CategoriesQuery);
$stmt->execute();
$CategoriesProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotiqueAfrique Homepage</title>
    <link rel="stylesheet" href="../assets/css/NewHomepage.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <style>
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
        overflow: auto;
    }

    /* Modal Content */
    .modal-content {
        background-color: brown; /* Brown background */
        margin: 10% auto; /* Center the modal */
        padding: 20px;
        border-radius: 10px;
        width: 70%; /* Adjust this value to change the size of the modal */
        max-width: 600px; /* Maximum width */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        color: white;
    }

    /* Modal Image */
    .modal-image {
        max-width: 100%; /* Ensure the image is responsive */
        height: auto;
        display: block;
        margin-bottom: 20px;
    }

    .modal-content img {
        max-width: 300px;
        margin-bottom: 20px;
    }

    .modal-actions {
        display: flex;
        flex-direction: column;
        gap: 10px; /* Space between buttons */
    }

    .close-modal {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 30px;
        cursor: pointer;
    }

    /* Modal Button Styles */
    .modal-actions button {
        background-color: #ff5733; /* Button background color */
        color: white; /* Text color */
        border: none; /* No border */
        padding: 10px 20px; /* Padding */
        font-size: 16px; /* Font size */
        cursor: pointer; /* Pointer on hover */
        border-radius: 5px; /* Rounded corners */
        transition: background-color 0.3s, transform 0.3s; /* Smooth transition */
    }

    .modal-actions button:hover {
        background-color: #c0392b; /* Darker shade on hover */
        transform: scale(1.05); /* Slightly scale on hover */
    }

    .modal-actions button:focus {
        outline: none; /* Remove outline */
        box-shadow: 0 0 0 2px rgba(255, 87, 51, 0.5); /* Focus outline */
    }
</style>
</head>
<body>
    <header>
        <div class="logo-nav">
            <h1 class="logo">BotiqueAfrique</h1>
            <nav>
                <a href="homepage.php">Home</a>
                <a href="Collections.php">Collections</a>
                <a href="checkout.php">Checkout</a>
                <a href="wishlist.php">Wishlist</a>
                <a href="useraccount.php">MyProfile</a>
                <a href="shoppingcart.php">Cart</a>
                <a href="sales.php">Sales</a>
                <a href="../functions/logout.php">Logout<a>
            </nav>
        </div>
    </header>
    <section class="banner">
        <div class="banner-content">
            <h2>Big Discount</h2>
            <h1>Authentic African Fashion</h1>
            <p>Up to 50% Off on Selected Items</p>
            <button onclick="window.location.href='Collections.php'">Shop Now</button>
        </div>
    </section>

    <section class="categories">
    <div class="product-list">
            <?php foreach ($CategoriesProducts as $product): 
                // Safely encode product details for JavaScript
                $productDetails = htmlspecialchars(json_encode([
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'image' => $product['image_url'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'item_type' => $product['item_type']
                ]), ENT_QUOTES, 'UTF-8');
            ?>
                <a href="#" class="product" data-product="<?= $productDetails ?>">
                    <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                    <p><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="trending-products">
        <h2>Trending African Styles</h2>
        <div class="product-list">
            <?php foreach ($trendingProducts as $product): 
                // Safely encode product details for JavaScript
                $productDetails = htmlspecialchars(json_encode([
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'image' => $product['image_url'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'item_type' => $product['item_type']
                ]), ENT_QUOTES, 'UTF-8');
            ?>
                <a href="#" class="product" data-product="<?= $productDetails ?>">
                    <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                    <p><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Product Modal -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <img id="modal-image" src="" alt="Product Image">
            <h2 id="modal-title"></h2>
            <p id="modal-description"></p>
            <p id="modal-price"></p>
            <div class="modal-actions">
                <button onclick="viewCollection()">View Collection</button>
                <button onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Add click event listeners to products
        document.addEventListener('DOMContentLoaded', function() {
            const products = document.querySelectorAll('.product');
            products.forEach(product => {
                product.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productData = JSON.parse(this.getAttribute('data-product'));
                    openProductModal(productData);
                });
            });
        });

        // Open product modal
        function openProductModal(product) {
            const modal = document.getElementById('product-modal');
            const modalImage = document.getElementById('modal-image');
            const modalTitle = document.getElementById('modal-title');
            const modalDescription = document.getElementById('modal-description');
            const modalPrice = document.getElementById('modal-price');

            modalImage.src = product.image;
            modalTitle.textContent = product.name;
            modalDescription.textContent = product.description;
            modalPrice.textContent = `Price: $${product.price}`;

            // Store current product type for collection view
            window.currentProductType = product.item_type;

            modal.style.display = 'block';
        }

        // Close modal
        function closeModal() {
            document.getElementById('product-modal').style.display = 'none';
        }

        // View Collection based on product type
        function viewCollection() {
            if (window.currentProductType) {
                // Redirect to collections page with filter
                window.location.href = `Collections.php?item_type=${window.currentProductType}`;
            } else {
                alert('Unable to determine collection type');
            }
        }

        // Close modal if clicked outside
        window.onclick = function(event) {
            const modal = document.getElementById('product-modal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>