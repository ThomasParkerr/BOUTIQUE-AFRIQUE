<?php
session_start();
include_once "../db/database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get cart_id and new quantity from the request
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    // Update the quantity in the cart in the database
    $sql = "UPDATE AfriqueBotique_Cart SET quantity = ? WHERE cart_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$quantity, $cart_id]);

    if ($stmt->rowCount() > 0) {
        // Successfully updated, send success response
        echo 'success';
    } else {
        // If update fails
        echo 'failure';
    }
}
?>
