<?php
// Include database connection
include('../db/database.php');

if (isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];

    // Remove product from cart
    $remove_query = "DELETE FROM AfriqueBotique_Cart WHERE cart_id = ?";
    $remove_stmt = $pdo->prepare($remove_query);

    if ($remove_stmt->execute([$cart_id])) {
        echo "success"; // Return success message
    } else {
        echo "failure"; // Return failure message
    }
}
?>
