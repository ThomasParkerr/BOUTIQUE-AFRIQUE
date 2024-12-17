<?php
// Path to your external CSS file
$cssFilePath = '../assets/css/Newuseraccount.css';

// Check if a form background color has been selected and update the CSS file
if (isset($_POST['form-background-color'])) {
    $formBackgroundColor = $_POST['form-background-color'];

    // Read the current contents of the CSS file
    $cssContent = file_get_contents($cssFilePath);

    // Replace the background color in the form selector
    $newCssContent = preg_replace(
        '/form\s{[^}]background-color:\s*[^;]+;/',
        'form { background-color: ' . $formBackgroundColor . ';',
        $cssContent
    );

    // Save the updated CSS content back to the file
    file_put_contents($cssFilePath, $newCssContent);

    // Reload the page to reflect the changes
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get the current form background color from the CSS file
$cssContent = file_get_contents($cssFilePath);

// Default form background color in case not found
$currentFormBackgroundColor = '#ffe0b2'; // Default form background color

// Extract form background color using regex
preg_match('/form\s{[^}]background-color:\s*([^;]+);/', $cssContent, $matches);
if (isset($matches[1])) {
    $currentFormBackgroundColor = $matches[1];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotiqueAfrique Customize</title>
    <link rel="stylesheet" href="../assets/css/Newuseraccount.css"> 
    <link rel="icon" href="../logo.png" type="image/x-icon"><!-- External CSS link -->
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
                <li><a href="StylePreference.php"><i class="fas fa-paint-brush"></i> Customize</a></li>
            </ul>
        </nav>

        <!-- Main Content Area -->
        <div class="main-content">
            <h1>Welcome</h1>
            <p>Choose a form background color:</p>

            <!-- Color Picker Form -->
            <form method="POST" action="">
                <div class="color-picker-container">
                    <label for="form-background-color-picker" class="color-picker-label">Form Background Color:</label>
                    <input type="color" id="form-background-color-picker" name="form-background-color" value="<?php echo $currentFormBackgroundColor; ?>">
                </div>

                <button type="submit" class="save-btn">Save Color</button>
            </form>
        </div>
    </div>
</body>
</html>