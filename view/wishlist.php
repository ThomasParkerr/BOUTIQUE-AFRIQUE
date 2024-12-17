<?php
// Include database connection
include('../db/database.php'); // Adjust the path as needed

// Start session to access session variables
session_start();

// Fetch the logged-in user's ID from the session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// If user is not logged in, redirect to login page
if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Fetch wishlist data from the database
$query = "SELECT w.wishlist_id, p.id AS product_id, p.name AS product_name, p.price, p.stock_quantity, p.image_url, p.discounted_price
          FROM AfriqueBotique_Wishlist w
          JOIN AfriqueBotique_Products p ON w.product_id = p.id
          WHERE w.user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - African Fashion</title>
    <link rel="stylesheet" href="../assets/css/Newwishlist.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="cart-wrap">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="main-heading">My Wishlist</div>
                    <div class="table-wishlist">
                        <table>
                            <thead>
                                <tr>
                                    <th width="50%">Product Name</th>
                                    <th width="15%">Unit Price</th>
                                    <th width="15%">Stock Status</th>
                                    <th width="15%">Action</th>
                                    <th width="10%">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($wishlist_items) > 0): ?>
                                    <?php foreach ($wishlist_items as $item): ?>
                                    <tr data-wishlist-id="<?= $item['wishlist_id']; ?>">
                                        <td>
                                            <div class="display-flex">
                                                <div class="img-product">
                                                    <img src="<?= $item['image_url']; ?>" alt="<?= $item['product_name']; ?>">
                                                </div>
                                                <div class="name-product"><?= $item['product_name']; ?></div>
                                            </div>
                                        </td>
                                        <td>
    <?php if ($item['discounted_price'] && $item['discounted_price'] < $item['price']): ?>
        <!-- Show only the discounted price if valid -->
        <span class="discounted-price">$<?= number_format($item['discounted_price'], 2); ?></span>
    <?php else: ?>
        <!-- Show normal price if no discount -->
        <span class="price">$<?= number_format($item['price'], 2); ?></span>
    <?php endif; ?>
</td>
                                        <td><span class="in-stock-box"><?= $item['stock_quantity']; ?></span></td>
                                        <td>
                                            <!-- Add to Cart Button -->
                                            <button class="round-black-btn" onclick="addToCart(<?= $item['product_id']; ?>)">Add to Cart</button>
                                        </td>
                                        <td>
                                            <!-- Remove from Wishlist -->
                                            <a href="javascript:void(0);" class="trash-icon" onclick="removeFromWishlist(<?= $item['wishlist_id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">Your wishlist is empty.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="back-btn-container">
                        <a href="useraccount.php" class="back-btn">Back to My Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Function to handle "Add to Cart" action
    function addToCart(productId) {
        const userId = <?php echo $user_id; ?>; // Use the logged-in user's ID

        // Prompt user to enter a quantity
        const quantity = prompt("Enter the quantity you want to add:", "1");

        // If user cancels or enters a non-numeric value, exit the function
        if (!quantity || isNaN(quantity) || quantity <= 0) {
            alert("Please enter a valid quantity.");
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../functions/add_to_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText); // Show response message
            }
        };

        // Send product details to the server with the user-defined quantity
        xhr.send(
            `user_id=${userId}&product_id=${productId}&quantity=${quantity}`
        );
    }

    // Function to handle "Remove from Wishlist" action using AJAX
    function removeFromWishlist(wishlistId) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "../functions/remove_from_wishlist.php?wishlist_id=" + wishlistId, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Check if the response indicates successful removal
                if (xhr.responseText.trim() === "Item removed from wishlist.") {
                    alert('Item removed from wishlist');

                    // Remove the corresponding table row from the frontend
                    const row = document.querySelector(`tr[data-wishlist-id='${wishlistId}']`);
                    if (row) {
                        row.remove(); // Remove the table row
                    }
                } else {
                    alert('Failed to remove item from wishlist');
                }
            }
        };
        xhr.send();
    }
</script>
</body>
</html>
