<?php
session_start();
include('../db/database.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to complete the checkout.";
    exit;
}

$userId = $_SESSION['user_id'];  // Get the currently logged-in user ID

// Get user data from session
$sessionFullName = $_SESSION['full_name'] ?? '';
$sessionAddress = $_SESSION['address'] ?? '';
$sessionCity = $_SESSION['city'] ?? '';
$sessionZipCode = $_SESSION['zip_code'] ?? '';
$sessionCountry = $_SESSION['country'] ?? '';

// Check if the submitted form matches session data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture the billing info submitted by the user
    $submittedFullName = $_POST['full_name'] ?? '';
    $submittedAddress = $_POST['address'] ?? '';
    $submittedCity = $_POST['city'] ?? '';
    $submittedZipCode = $_POST['zip_code'] ?? '';
    $submittedCountry = $_POST['country'] ?? '';

    // Compare the session data with the submitted billing data
    if ($submittedFullName !== $sessionFullName || $submittedAddress !== $sessionAddress || 
        $submittedCity !== $sessionCity || $submittedZipCode !== $sessionZipCode || 
        $submittedCountry !== $sessionCountry) {
        
        // If there's a mismatch, prompt the user to correct the information
        echo "The provided information doesn't match your saved details. Please check and try again.";
        exit;
    }
}

// Proceed with the checkout process if the user credentials match
try {
    // Start a transaction to update the checkout items
    $pdo->beginTransaction();

    // Update checkout status for items in the user's cart to "Yes"
    $updateQuery = "UPDATE AfriqueBotique_Cart SET checkout = 'Yes' WHERE user_id = :user_id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute(['user_id' => $userId]);

    // Commit the transaction
    $pdo->commit();

    // Redirect to homepage after successful checkout
    header("Location: ../view/homepage.php?success=Checkout completed successfully.");
} catch (PDOException $e) {
    // Rollback the transaction if something goes wrong
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
