<?php
    // Start the session
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Redirect to login if not logged in
        exit;
    }

    if (isset($_SESSION['success'])) {
        $successMessage = $_SESSION['success'];
        echo "<script>alert('$successMessage');</script>";
    
        // Clear the session message after it is displayed
        unset($_SESSION['success']);
    }

    // Retrieve user session data
    $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
    $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
    $payment_id = isset($_SESSION['payment_id']) ? $_SESSION['payment_id'] : ''; // Unique payment record identifier
    $full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : ''; // Full name of the customer
    $address = isset($_SESSION['address']) ? $_SESSION['address'] : ''; // Address of the customer
    $city = isset($_SESSION['city']) ? $_SESSION['city'] : ''; // City of the customer
    $zip_code = isset($_SESSION['zip_code']) ? $_SESSION['zip_code'] : ''; // Zip code of the customer
    $country = isset($_SESSION['country']) ? $_SESSION['country'] : ''; // Country of the customer
    $payment_method = isset($_SESSION['payment_method']) ? $_SESSION['payment_method'] : '';// Payment method (Credit Card / Bank Transfer)

// For Credit Card information, only if selected
$card_number = isset($_SESSION['card_number']) ? $_SESSION['card_number'] : '';
$holder_name = isset($_SESSION['holder_name']) ? $_SESSION['holder_name'] : '';
$exp_date = isset($_SESSION['exp_date']) ? $_SESSION['exp_date'] : '';

// For Bank Transfer information, only if selected
$bank_name = isset($_SESSION['bank_name']) ? $_SESSION['bank_name'] : '';
$account_number = isset($_SESSION['account_number']) ? $_SESSION['account_number'] : '';

// Ensure that all session data is sanitized
$full_name = htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8');
$address = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');
$city = htmlspecialchars($city, ENT_QUOTES, 'UTF-8');
$zip_code = htmlspecialchars($zip_code, ENT_QUOTES, 'UTF-8');
$country = htmlspecialchars($country, ENT_QUOTES, 'UTF-8');
$payment_method = htmlspecialchars($payment_method, ENT_QUOTES, 'UTF-8');
$card_number = htmlspecialchars($card_number, ENT_QUOTES, 'UTF-8');
$holder_name = htmlspecialchars($holder_name, ENT_QUOTES, 'UTF-8');
$exp_date = htmlspecialchars($exp_date, ENT_QUOTES, 'UTF-8');
$bank_name = htmlspecialchars($bank_name, ENT_QUOTES, 'UTF-8');
$account_number = htmlspecialchars($account_number, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotiqueAfrique Payment Options</title>
    <link rel="stylesheet" href="../assets/css/Newuseraccount.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../logo.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <!-- Side Navigation Pane -->
        <nav class="side-nav">
            <h2>Account</h2>
            <h3><a href="homepage.php" id="go-back-home" style="color: white; font-size: 18px; font-weight: bold; text-decoration: none; padding: 5px 15px; border-radius: 5px; display: inline-block; transition: background-color 0.3s ease, color 0.3s ease;">Go back Home</a></h3>
            <ul>
                <li><a href="useraccount.php" class="active" data-section="personal-info"><i class="fas fa-user"></i> Personal Info</a></li>
                <li><a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                <li><a href="PurchaseHistory.php" data-section="purchase-history"><i class="fas fa-shopping-cart"></i> Purchase History</a></li>
                <li><a href="Loyalty.php" data-section="loyalty-points"><i class="fas fa-gift"></i> Loyalty Points</a></li>
                <li><a href="PaymentSettings.php" data-section="payment-options"><i class="fas fa-credit-card"></i> Payment Options</a></li>
                <li><a href="Security.php" data-section="security-settings"><i class="fas fa-shield-alt"></i> Security Settings</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Payment Options</h1>
            <form id="payment-options-form" method="POST" action="../functions/UpdatePayments.php" novalidate>
                <div class="form-group">
                    <label for="full-name">Full Name</label>
                    <input type="text" id="full-name" name="full_name" value="<?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>" 
                           required aria-required="true" autocomplete="name"
                           pattern="[A-Za-z\s]+" title="Letters and spaces only">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" 
                           required aria-required="true" autocomplete="street-address">
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" 
                           required aria-required="true" autocomplete="address-level2"
                           pattern="[A-Za-z\s]+" title="Letters and spaces only">
                </div>
                <div class="form-group">
                    <label for="zip-code">Zip Code</label>
                    <input type="text" id="zip-code" name="zip_code" value="<?php echo htmlspecialchars($zip_code); ?>" 
                           required aria-required="true" autocomplete="postal-code"
                           pattern="\d{5}" title="5-digit zip code">
                </div>
                <div class="form-group">
                    <label for="country">Country</label>
                    <select id="country" name="country" required aria-required="true">
                        <option value="USA" <?php echo ($country === 'USA') ? 'selected' : ''; ?>>United States</option>
                        <option value="NGA" <?php echo ($country === 'NGA') ? 'selected' : ''; ?>>Nigeria</option>
                    </select>
                </div>

                <!-- Payment Method Selector -->
                <div class="form-group">
                    <label for="payment-method">Payment Method</label>
                    <select id="payment-method" name="payment_method" required aria-required="true">
                        <option value="Credit Card" <?php echo ($payment_method === 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                        <option value="Bank Transfer" <?php echo ($payment_method === 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                    </select>
                </div>

                <!-- Credit Card Information -->
                <div class="card-info">
                    <h4>Credit Card Info</h4>
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" name="card_number" 
                               value="<?php echo htmlspecialchars($card_number); ?>" 
                               placeholder="**** **** **** ****"
                               required aria-required="true" 
                               pattern="^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$"
                               title="16-digit card number"
                               autocomplete="cc-number">
                    </div>
                    <div class="form-group">
                        <label for="holder-name">Cardholder Name</label>
                        <input type="text" id="holder-name" name="holder_name" 
                               value="<?php echo htmlspecialchars($holder_name); ?>" required aria-required="true"
                               pattern="[A-Za-z\s]+" title="Letters and spaces only"
                               autocomplete="cc-name">
                    </div>
                </div>

                <!-- Bank Transfer Information -->
                <div id="bank-transfer-info">
                    <h4>Bank Transfer Info</h4>
                    <div class="form-group">
                        <label for="bank-name">Bank Name</label>
                        <input type="text" id="bank-name" name="bank_name" 
                               value="<?php echo htmlspecialchars($bank_name); ?>" 
                               placeholder="Bank Name" required aria-required="true"
                               pattern="[A-Za-z\s]+" title="Letters and spaces only">
                    </div>
                    <div class="form-group">
                        <label for="account-number">Account Number</label>
                        <input type="text" id="account-number" name="account_number" 
                               value="<?php echo htmlspecialchars($account_number); ?>" 
                               placeholder="123456789" required aria-required="true"
                               pattern="\d{9,18}" title="9-18 digit account number">
                    </div>
                </div>

                <button type="submit" class="save-btn">Update Payment Methods</button>
            </form>
        </div>
    </div>
</body>
</html>
