<?php
    // Start the session
    session_start();

    include('../db/database.php');

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Redirect to login if not logged in
        exit;
    }

    // Retrieve user session data
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $firstname = $_SESSION['first_name'];
    $lastname = $_SESSION['last_name'];
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    $phone = isset($_SESSION['phone_number']) ? $_SESSION['phone_number'] : ''; // Check if phone number exists in session
    $address = isset($_SESSION['address']) ? $_SESSION['address'] : '';

    // Ensure that all session data is sanitized
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $firstname = htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8');
    $lastname = htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');
?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account</title>
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
            <h1>User Account</h1>

            <!-- Personal Info Section -->
            <section id="personal-info" class="section-content active">
                <h2>Personal Information</h2>
                <form id="user-profile-form" method="POST" action="../functions/updateProfile.php">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Shipping Address</label>
                        <input type="text" id="address" name="address" value="<?php echo $address; ?>">
                    </div>
                    <button type="submit" class="save-btn">Save Changes</button>
                </form>
            </section>
        </div>
    </div>
</body>
</html>
