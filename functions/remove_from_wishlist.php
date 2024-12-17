<?php
// Include database connection
include('../db/database.php');

// Check if the cart item ID is set in the request
if (isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];

    // Remove product from cart
    $remove_query = "DELETE FROM AfriqueBotique_Cart WHERE cart_id = ?";
    $remove_stmt = $pdo->prepare($remove_query);

    // Execute the query to remove the item from the cart
    if ($remove_stmt->execute([$cart_id])) {
        echo "Item removed from cart."; // Return success message
    } else {
        echo "Failed to remove item from cart."; // Return failure message
    }
}
?>
