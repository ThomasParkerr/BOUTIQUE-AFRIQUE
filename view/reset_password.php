<?php
session_start();
include("../db/database.php"); // Make sure your database connection is correct

// Check if the user is coming from the security questions page
if (!isset($_SESSION['reset_email'])) {
    echo "<script>alert('Session expired. Please try again.'); window.location.href='password_reset.php';</script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve new password and confirmation
    $newPassword = isset($_POST['newPassword']) ? trim($_POST['newPassword']) : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : '';

    // Validate new password
    if (empty($newPassword) || empty($confirmPassword)) {
        echo "<script>alert('Both fields are required.'); window.history.back();</script>";
        exit();
    }

    // Check if passwords match
    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Validate password strength (example: at least 8 characters)
    if (strlen($newPassword) < 8) {
        echo "<script>alert('Password must be at least 8 characters long.'); window.history.back();</script>";
        exit();
    }

    try {
        // Connect to the database using PDO
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $stmt = $pdo->prepare("UPDATE AfriqueBotique_Users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedNewPassword, $_SESSION['reset_email']]);

        // Success message and redirection to login page
        unset($_SESSION['reset_email']); // Clear the session data
        echo "<script>alert('Password reset successfully! You can now log in with your new password.'); window.location.href='login.php';</script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>alert('Error resetting password. Please try again later.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <style>
        <?php include '../assets/css/forgotpass.css'; ?>
    </style>
</head>
<body>
	<div class="row">
		<h1>Reset Password</h1>
		<h6 class="information-text">Enter your new password below.</h6>
		<form action="" method="POST">
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" name="newPassword" id="newPassword" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" name="confirmPassword" id="confirmPassword" required>
            </div>
            <button type="submit">Reset Password</button>
        </form>
		<div class="footer">
			<h5>Remember your password? <a href="login.php">Sign In.</a></h5>
			<p class="information-text"></p>
		</div>
	</div>
</body>
</html>
