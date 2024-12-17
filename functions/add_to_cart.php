<?php

session_start(); // Ensure session is started

include_once "../db/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prefer using session if available
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_POST['user_id']) ? intval($_POST['user_id']) : null);
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Debugging: Check if user_id is correctly set
    if (!$userId) {
        echo "User ID is not set properly.";
        exit;
    }

    if ($userId && $productId) {
        try {
            // Check if the user exists in the Users table
            $userCheckSql = "SELECT 1 FROM AfriqueBotique_Users WHERE user_id = :user_id";
            $userCheckStmt = $pdo->prepare($userCheckSql);
            $userCheckStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $userCheckStmt->execute();
            
            if ($userCheckStmt->rowCount() == 0) {
                // User does not exist, return error
                echo "User not found.";
                exit;
            }

            // Fetch the product's original price and sale details
            $sql = "SELECT p.price, p.discounted_price, s.discount_percentage 
                    FROM AfriqueBotique_Products p
                    LEFT JOIN AfriqueBotique_Sales s ON p.id = s.product_id AND NOW() BETWEEN s.start_date AND s.end_date
                    WHERE p.id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                // Calculate discounted price if there's an active sale
                if (!empty($product['discount_percentage']) && !is_null($product['discount_percentage'])) {
                    // Calculate the discounted price
                    $discountAmount = ($product['price'] * $product['discount_percentage']) / 100;
                    $discountedPrice = $product['price'] - $discountAmount;

                    // Update the product's discounted price in the database
                    $updateSql = "UPDATE AfriqueBotique_Products 
                                  SET discounted_price = :discounted_price 
                                  WHERE id = :product_id";
                    $updateStmt = $pdo->prepare($updateSql);
                    $updateStmt->bindParam(':discounted_price', $discountedPrice, PDO::PARAM_STR);
                    $updateStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                    $updateStmt->execute();
                } else {
                    // If no discount, ensure discounted_price is null
                    $discountedPrice = null;
                }

                // Now insert the product into the cart with the correct price
                $priceToUse = $discountedPrice ?? $product['price'];

                // Insert the product with the correct quantity into the AfriqueBotique_Cart table
                $sql = "INSERT INTO AfriqueBotique_Cart (user_id, product_id, quantity) 
                        VALUES (:user_id, :product_id, :quantity)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    echo "Product successfully added to cart!";
                } else {
                    echo "Failed to add product to cart.";
                }
            } else {
                echo "Product not found.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "Invalid input data.";
    }
} else {
    echo "Invalid request method.";
}
?>
