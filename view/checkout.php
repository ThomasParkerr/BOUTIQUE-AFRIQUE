<?php
session_start();
include('../db/database.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to complete the checkout.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if user has any items in the cart
$cart_query = "SELECT * FROM AfriqueBotique_Cart WHERE user_id = :user_id AND checkout = 'No'";
$stmt = $pdo->prepare($cart_query);
$stmt->execute(['user_id' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header("Location: NoCart.php");
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch the submitted form data
    $full_name = $_POST['full_name'] ?? null;
    $address = $_POST['address'] ?? null;
    $city = $_POST['city'] ?? null;
    $zip_code = $_POST['zip_code'] ?? null;
    $country = $_POST['country'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;

    // Basic validation: Ensure all required fields are filled

    // Additional payment details validation (if applicable)
    if ($payment_method == 'Credit Card') {
        $card_number = $_POST['card_number'] ?? null;
        $holder_name = $_POST['holder_name'] ?? null;

        if (!$card_number || !$holder_name) {
            echo "<script>
                    alert('Please fill in all credit card details.');
                    window.location.href = 'checkout.php';
                  </script>";
            exit;
        }
    } elseif ($payment_method == 'Bank Transfer') {
        $bank_name = $_POST['bank_name'] ?? null;
        $account_number = $_POST['account_number'] ?? null;

        if (!$bank_name || !$account_number) {
            echo "<script>
                    alert('Please fill in all bank transfer details.');
                    window.location.href = 'checkout.php';
                  </script>";
            exit;
        }
    }

    // Proceed with checkout update
    $update_query = "UPDATE AfriqueBotique_Cart SET checkout = 'Yes' WHERE user_id = :user_id AND checkout = 'No'";
    $stmt = $pdo->prepare($update_query);
    if ($stmt->execute(['user_id' => $user_id])) {
        echo "<script>
                alert('Checkout successful!');
                window.location.href = '../view/homepage.php';
              </script>";
    } else {
        echo "Error during checkout: " . implode(", ", $stmt->errorInfo());
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotiqueAfrique Checkout Page</title>
    <link rel="stylesheet" href="../assets/css/Newercheckout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <script>
        function togglePaymentFields(paymentMethod) {
            const cardFields = document.getElementById("card-info");
            const bankTransferFields = document.getElementById("bank-transfer-info");

            cardFields.style.display = "none";
            bankTransferFields.style.display = "none";

            if (paymentMethod === 'Credit Card') {
                cardFields.style.display = "block";
            } else if (paymentMethod === 'Bank Transfer') {
                bankTransferFields.style.display = "block";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Checkout</h1>
            <p>Please provide your payment details to complete the checkout process.</p>
        </header>

        <div id="payment-methods">
            <label>
                <input type="radio" name="payment_method" value="Credit Card" onclick="togglePaymentFields('Credit Card')" required>
                <i class="fas fa-credit-card fa-3x"></i> Credit Card
            </label>
            <label>
                <input type="radio" name="payment_method" value="Bank Transfer" onclick="togglePaymentFields('Bank Transfer')" required>
                <i class="fas fa-university fa-3x"></i> Bank Transfer
            </label>
        </div>

        <div id="info">
            <form action="checkout.php" method="POST" id="payment-form">
                <div class="billing-info">
                    <h4>Billing Information</h4>
                    <div>
                        <label for="full-name">Full Name</label>
                        <input type="text" id="full-name" name="full_name" placeholder="John Doe" required>
                    </div>
                    <div>
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" placeholder="123 Main St." required>
                    </div>
                    <div>
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" placeholder="City Name" required>
                    </div>
                    <div>
                        <label for="zip-code">Zip Code</label>
                        <input type="text" id="zip-code" name="zip_code" placeholder="12345" required>
                    </div>
                    <div>
                        <label for="country">Country</label>
                        <select id="country" name="country" required>
                            <option value="USA">United States</option>
                            <option value="NGA">Nigeria</option>
                            <!-- Add more countries as needed -->
                        </select>
                    </div>
                </div>

                <div id="card-info" style="display: none;">
                    <h4>Credit Card Information</h4>
                    <div>
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456">
                    </div>
                    <div>
                        <label for="holder-name">Cardholder Name</label>
                        <input type="text" id="holder-name" name="holder_name" placeholder="John Doe">
                    </div>
                </div>

                <div id="bank-transfer-info" style="display: none;">
                    <h4>Bank Transfer Information</h4>
                    <div>
                        <label for="bank-name">Bank Name</label>
                        <input type="text" id="bank-name" name="bank_name" placeholder="Bank Name">
                    </div>
                    <div>
                        <label for="account-number">Account Number</label>
                        <input type="text" id="account-number" name="account_number" placeholder="123456789">
                    </div>
                </div>

                <div class="buttons">
                    <button class="checkout" type="submit">Complete Checkout</button>
                    <button class="exit" type="button" onclick="window.location.href='../view/homepage.php'">Back to Homepage</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
