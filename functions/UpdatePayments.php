<?php
session_start();
include('../db/database.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $fullName = $_POST['full_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zipCode = $_POST['zip_code'];
    $country = $_POST['country'];
    $paymentMethod = $_POST['payment_method']; // 'Credit Card' or 'Bank Transfer'

    // Get the currently logged-in user's ID from the session
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } else {
        echo "User not logged in.";
        exit;
    }

    // Conditional data based on payment method
    if ($paymentMethod == 'Credit Card') {
        $cardNumber = $_POST['card_number'];
        $holderName = $_POST['holder_name'];
        $bankName = null;
        $accountNumber = null;
    } elseif ($paymentMethod == 'Bank Transfer') {
        $cardNumber = null;
        $holderName = null;
        $bankName = $_POST['bank_name'];
        $accountNumber = $_POST['account_number'];
    } else {
        // Handle invalid payment method
        echo "Invalid payment method selected.";
        exit;
    }

    // Validate inputs (e.g., check if payment info is valid)
    // Add additional validation logic here if necessary.

    try {
        // Check if the user already has a payment record
        $checkQuery = "SELECT * FROM AfriqueBotique_payments WHERE user_id = :user_id";
        $stmt = $pdo->prepare($checkQuery);
        $stmt->execute(['user_id' => $userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingRecord) {
            // If the record exists, update the details
            $query = "
                UPDATE AfriqueBotique_payments 
                SET 
                    full_name = :full_name, 
                    address = :address, 
                    city = :city, 
                    zip_code = :zip_code, 
                    country = :country, 
                    payment_method = :payment_method,
                    card_number = :card_number, 
                    holder_name = :holder_name, 
                    bank_name = :bank_name, 
                    account_number = :account_number
                WHERE user_id = :user_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'user_id' => $userId,
                'full_name' => $fullName,
                'address' => $address,
                'city' => $city,
                'zip_code' => $zipCode,
                'country' => $country,
                'payment_method' => $paymentMethod,
                'card_number' => $cardNumber,
                'holder_name' => $holderName,
                'bank_name' => $bankName,
                'account_number' => $accountNumber,
            ]);

            // Redirect to the same page and trigger a success message via alert
            $_SESSION['success'] = 'Payment methods updated successfully';
            header("Location: ../view/PaymentSettings.php");
            exit;
        } else {
            // If the record does not exist, insert a new row
            $query = "
                INSERT INTO AfriqueBotique_payments 
                (user_id, full_name, address, city, zip_code, country, payment_method, 
                card_number, holder_name, bank_name, account_number)
                VALUES 
                (:user_id, :full_name, :address, :city, :zip_code, :country, :payment_method, 
                :card_number, :holder_name, :bank_name, :account_number)
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'user_id' => $userId,
                'full_name' => $fullName,
                'address' => $address,
                'city' => $city,
                'zip_code' => $zipCode,
                'country' => $country,
                'payment_method' => $paymentMethod,
                'card_number' => $cardNumber,
                'holder_name' => $holderName,
                'bank_name' => $bankName,
                'account_number' => $accountNumber,
            ]);

            // Redirect to the same page and trigger a success message via alert
            $_SESSION['success'] = 'Payment methods saved successfully';
            header("Location: ../view/PaymentSettings.php");
            exit;
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
