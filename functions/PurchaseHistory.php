<?php
include_once "../db/database.php";

session_start(); // Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Replace with dynamic user ID from session or authentication
$user_id = $_SESSION['user_id'];

// Query to get purchase history with product details, including discount price if available
$query = "SELECT p.name AS product_name, c.quantity, p.price, p.discounted_price, c.date_added 
          FROM AfriqueBotique_Cart c
          JOIN AfriqueBotique_Products p ON c.product_id = p.id
          WHERE c.user_id = :user_id AND c.checkout = 'Yes'";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$purchase_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History</title>
    <link rel="stylesheet" href="../assets/css/purchasehistory.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../logo.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <!-- Purchase History Section -->
        <div class="back-button">
            <a href="javascript:history.back()" class="back-btn"><i class="fa fa-arrow-left"></i> Back to Account</a>
        </div>

        <section id="purchase-history" class="section-content">
            <h2>Purchase History</h2>
            <ul class="purchase-history">
                <?php if (count($purchase_history) > 0): ?>
                    <?php foreach ($purchase_history as $purchase): ?>
                        <?php
                            // Use the discount_price if available, otherwise use the regular price
                            $price_to_use = ($purchase['discounted_price'] !== null) ? $purchase['discounted_price'] : $purchase['price'];

                            // Calculate the total cost using the price (regular or discounted)
                            $total_price = $price_to_use * $purchase['quantity'];
                        ?>
                        <li>
                            <span><strong>Product:</strong> <?= htmlspecialchars($purchase['product_name']); ?> - 
                                <?= htmlspecialchars($purchase['quantity']); ?> items</span>
                            <span>Price: $<?= number_format($price_to_use, 2); ?> each</span>
                            <span>Total: $<?= number_format($total_price, 2); ?></span>
                            <span>Date: <?= htmlspecialchars($purchase['date_added']); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><em>No purchase history found.</em></li>
                <?php endif; ?>
            </ul>
        </section>
    </div>
</body>
</html>
