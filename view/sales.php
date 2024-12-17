<?php
session_start(); 
// Include the database connection file
include_once "../db/database.php";
// Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Prepare the SQL query to fetch products that are on sale, including the discount percentage
$sql = "SELECT p.*, s.discount_percentage, s.start_date, s.end_date
        FROM AfriqueBotique_Products p
        JOIN AfriqueBotique_Sales s ON p.id = s.product_id AND NOW() BETWEEN s.start_date AND s.end_date"; // Only select products on sale

// Prepare and execute the statement
$stmt = $pdo->prepare($sql);
$stmt->execute();

$products = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Calculate discount price if discount percentage is available
    $originalPrice = floatval($row["price"]);
    $discountPercentage = floatval($row["discount_percentage"]);
    $discountPrice = $originalPrice - ($originalPrice * ($discountPercentage / 100));
    
    // Store the product data including the calculated discount price
    $row["discount_price"] = $discountPrice;
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoutiqueAfrique - Products</title>
    <link rel="stylesheet" href="../assets/css/Salesss.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
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
                <a href="../functions/logout.php">Logout<a>
            </nav>
        </div>
    </header>

<section class="banner">
    <div class="banner-content">
        <h2>Big Discount</h2>
        <h1>Authentic African Fashion</h1>
        <p>Up to 50% Off on Selected Items</p>
    </div>
</section>

<section class="categories" id="product-list">
    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <?php foreach ($products as $product) {
            // Carefully escape special characters to prevent breaking JavaScript
            $productId = htmlspecialchars($product["id"], ENT_QUOTES);
            $productTitle = htmlspecialchars($product["name"], ENT_QUOTES);
            $imageUrl = htmlspecialchars($product["image_url"], ENT_QUOTES);
            $productDescription = htmlspecialchars($product["description"], ENT_QUOTES, 'UTF-8');
            $productOriginalPrice = floatval($product["price"]);
            $productDiscountPrice = floatval($product["discount_price"]);
            $productStock = intval($product["stock_quantity"]);
            $discountPercentage = floatval($product["discount_percentage"]);
            
            // Display product with sale banner showing discount percentage
            echo '<div class="product-container">
                    <a href="#" class="product" data-product="' . 
                         htmlspecialchars(json_encode([ 
                             'id' => $productId, 
                             'title' => $productTitle, 
                             'image' => $imageUrl, 
                             'description' => $productDescription, 
                             'price' => $productDiscountPrice, // Add the discounted price
                             'original_price' => $productOriginalPrice, 
                             'stock' => $productStock 
                         ]), ENT_QUOTES) . '">
                        <div class="sale-banner">-' . $discountPercentage . '%</div>
                        <img src="' . $imageUrl . '" alt="' . $productTitle . '">
                        <p>' . $productTitle . '</p>
                    </a>
                </div>';
        } ?>
    <?php endif; ?>
</section>

<div id="product-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modal-title" class="modal-title">Product Details</h2>
        <div class="modal-body">
            <img id="modal-image" class="modal-image" src="" alt="Product Image">
            <div class="modal-text">
                <p id="modal-description">Product description goes here.</p>
                <p id="modal-original-price">Original Price: $0.00</p>
                <p id="modal-price">Discount Price: $0.00</p>
                <p id="modal-stock">Stock: 0</p>
                <div class="modal-actions">
                    <button id="add-to-cart" class="btn african-btn" onclick="addToCart()">Add to Cart</button>
                    <button id="add-to-wishlist" class="btn african-btn" onclick="addToWishlist()">Add to Wishlist</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variable to track selected product
    let selectedProduct = {};

    // Get user ID from PHP session
    const userId = <?php echo $_SESSION['user_id']; ?>;

    // Add event listener to all product links after the page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Select all product links
        const productLinks = document.querySelectorAll('.product');
        
        // Add click event to each product link
        productLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default link behavior
                
                // Parse the product data from the data attribute
                const productData = JSON.parse(this.getAttribute('data-product'));
                
                // Call show modal with parsed data
                showModal(
                    productData.id, 
                    productData.title, 
                    productData.image, 
                    productData.description, 
                    productData.original_price, // Original Price
                    productData.price, // Discount Price
                    productData.stock
                );
            });
        });
    });

    // Show product modal with dynamic data
    function showModal(productId, title, imageSrc, description, originalPrice, price, stock) {
        selectedProduct = { productId, title, imageSrc, description, originalPrice, price, stock };
        document.getElementById('product-modal').style.display = 'block';
        document.getElementById('modal-title').textContent = title;
        document.getElementById('modal-image').src = imageSrc;
        document.getElementById('modal-description').textContent = description;
        document.getElementById('modal-original-price').textContent = `Original Price: $${originalPrice.toFixed(2)}`;
        document.getElementById('modal-price').textContent = `Discount Price: $${price.toFixed(2)}`;
        document.getElementById('modal-stock').textContent = `Stock: ${stock}`;
    }

    // Close modal
    function closeModal() {
        document.getElementById('product-modal').style.display = 'none';
    }

    // Add product to cart
    function addToCart() {
        const quantity = 1; // Default quantity to 1 for now
        
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../functions/add_to_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText); // Show response message
            }
        };
        
        // Send product details to the server (with discounted price)
        xhr.send(
            `user_id=${userId}&product_id=${selectedProduct.productId}&quantity=${quantity}&price=${selectedProduct.price}`
        );
        
        closeModal();
    }

    // Add product to wishlist
    function addToWishlist() {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../functions/add_to_wishlist.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText); // Show response message
            }
        };
        
        // Send product details to the server
        xhr.send(
            `user_id=${userId}&product_id=${selectedProduct.productId}`
        );
        
        closeModal();
    }
</script>

</body>
</html>
