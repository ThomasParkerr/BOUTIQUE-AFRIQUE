<?php
session_start(); // Ensure the session is started

include_once "../db/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user_id from session instead of POST
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;

    // Ensure user is logged in and product_id is valid
    if ($userId && $productId) {
        try {
            // Debug: output userId and productId to check if they are set correctly
            // echo "User ID: $userId, Product ID: $productId<br>";

            // Insert into AfriqueBotique_Wishlist table
            $sql = "INSERT INTO AfriqueBotique_Wishlist (user_id, product_id) 
                    VALUES (:user_id, :product_id)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            
            // Debug: output SQL query and check if it runs
            // echo "Executing SQL query: $sql<br>";
            
            if ($stmt->execute()) {
                echo "Product successfully added to wishlist!";
            } else {
                echo "Failed to add product to wishlist.";
                // Debugging: Check the actual error from the database
                print_r($stmt->errorInfo());
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        // Check if user is not logged in or product_id is missing
        if (!$userId) {
            echo "User is not logged in.";
        } elseif (!$productId) {
            echo "Invalid product ID.";
        } else {
            echo "Invalid input data.";
        }
    }
} else {
    echo "Invalid request method.";
}
?>
