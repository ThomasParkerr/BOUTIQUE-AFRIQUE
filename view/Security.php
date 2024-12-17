<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings</title>
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
            <h1>Security Settings</h1>

            <!-- Security Settings Section -->
            <section id="security-settings" class="section-content active">
                <h2>Update Password</h2>
                <form id="security-settings-form" method="POST" action="../functions/updateSecurity.php">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                    </div>
                    <button type="submit" class="save-btn">Update Password</button>
                </form>
            </section>
        </div>
    </div>
</body>
</html>
