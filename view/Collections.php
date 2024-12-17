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

// Initialize filter variables
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;
$itemTypeFilter = isset($_GET['item_type']) ? $_GET['item_type'] : null;

// Prepare the SQL query with dynamic filtering
$sql = "SELECT * FROM AfriqueBotique_Products WHERE 1=1";
$params = [];

// Add category filter if specified
if ($categoryFilter) {
    $sql .= " AND product_type = :category";
    $params[':category'] = $categoryFilter;
}

// Add item type filter if specified
if ($itemTypeFilter) {
    $sql .= " AND item_type = :item_type";
    $params[':item_type'] = $itemTypeFilter;
}

// Prepare and execute the statement
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $products[] = $row;
}

// Function to generate filter URL
function generateFilterURL($category = null, $itemType = null) {
    $url = '?';
    $params = [];
    
    if ($category) {
        $params[] = "category=" . urlencode($category);
    }
    
    if ($itemType) {
        $params[] = "item_type=" . urlencode($itemType);
    }
    
    return $url . implode('&', $params);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotiqueAfrique Collection</title>
    <link rel="stylesheet" href="../assets/css/NewCollections.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <style>
        .active-filter {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
<header>
        <div class="logo-nav">
            <h1 class="logo">Boutique Afrique</h1>
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
        <a href="<?= generateFilterURL('Men') ?>" class="button <?= $categoryFilter === 'Men' ? 'active-filter' : '' ?>">Men</a>
        <a href="<?= generateFilterURL('Women') ?>" class="button <?= $categoryFilter === 'Women' ? 'active-filter' : '' ?>">Women</a>
    </div>
    <div class="banner-content">
        <a href="<?= generateFilterURL($categoryFilter, 'Dashiki') ?>" class="button <?= $itemTypeFilter === 'Dashiki' ? 'active-filter' : '' ?>">Dashiki</a>
        <a href="<?= generateFilterURL($categoryFilter, 'Ankara') ?>" class="button <?= $itemTypeFilter === 'Ankara' ? 'active-filter' : '' ?>">Ankara</a>
        <a href="<?= generateFilterURL($categoryFilter, 'Headwrap') ?>" class="button <?= $itemTypeFilter === 'Headwrap' ? 'active-filter' : '' ?>">Headwrap</a>
        <a href="<?= generateFilterURL($categoryFilter, 'Bags') ?>" class="button <?= $itemTypeFilter === 'Bags' ? 'active-filter' : '' ?>">Bags</a>
        <a href="<?= generateFilterURL($categoryFilter, 'Jewelry') ?>" class="button <?= $itemTypeFilter === 'Jewelry' ? 'active-filter' : '' ?>">Jewelry</a>
        <a href="<?= generateFilterURL($categoryFilter, 'Kente') ?>" class="button <?= $itemTypeFilter === 'Kente' ? 'active-filter' : '' ?>">Kente</a>
        <a href="?" class="button">Reset</a>
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
            $productPrice = floatval($product["price"]);
            $productStock = intval($product["stock_quantity"]);
            $productCategory = htmlspecialchars($product["product_type"] . '-' . $product["item_type"], ENT_QUOTES);
            
            // Use JSON encoding to safely pass product details
            echo '<a href="#" class="product ' . $productCategory . '" data-product="' . 
                 htmlspecialchars(json_encode([
                     'id' => $productId,
                     'title' => $productTitle,
                     'image' => $imageUrl,
                     'description' => $productDescription,
                     'price' => $productPrice,
                     'stock' => $productStock
                 ]), ENT_QUOTES) . '">
                    <img src="' . $imageUrl . '" alt="' . $productTitle . '">
                    <p>' . $productTitle . '</p>
                 </a>';
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
                <p id="modal-price">Price: $0.00</p>
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
                    productData.price, 
                    productData.stock
                );
            });
        });
    });

    // Show product modal with dynamic data
    function showModal(productId, title, imageSrc, description, price, stock) {
        selectedProduct = { productId, title, imageSrc, description, price, stock };
        document.getElementById('product-modal').style.display = 'block';
        document.getElementById('modal-title').textContent = title;
        document.getElementById('modal-image').src = imageSrc;
        document.getElementById('modal-description').textContent = description;
        document.getElementById('modal-price').textContent = `Price: $${price.toFixed(2)}`;
        document.getElementById('modal-stock').textContent = `Stock: ${stock}`;
    }

    // Close modal
    function closeModal() {
        document.getElementById('product-modal').style.display = 'none';
    }

    // Add product to cart
    // Add product to cart
function addToCart() {
    const userId = 1; // Replace with the logged-in user's ID
    const quantity = 1; // Default quantity to 1 for now
    
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../functions/add_to_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert(xhr.responseText); // Show response message
        }
    };
    
    // Send product details to the server
    xhr.send(
        `user_id=${userId}&product_id=${selectedProduct.productId}&quantity=${quantity}`
    );
    
    closeModal();
}

function addToWishlist() {
    const userId = 1; // Replace with the logged-in user's ID
    
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


    // Update stock in the database
    function updateStockInDatabase(productId, newStock) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log('Stock updated successfully');
            }
        };
        xhr.send("product_id=" + productId + "&new_stock=" + newStock);
    }

    // Stock update PHP code
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $product_id = $_POST['product_id'];
        $new_stock = $_POST['new_stock'];

        try {
            $sql = "UPDATE AfriqueBotique_Products SET stock_quantity = :new_stock WHERE id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':new_stock', $new_stock, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "Stock updated successfully";
            } else {
                echo "Error updating stock";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    ?>
</script>

</body>
</html>