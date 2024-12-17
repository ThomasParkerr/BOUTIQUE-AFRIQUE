<?php
session_start();
include("../db/database.php"); // Make sure your database connection is correct

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve email
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (!empty($email)) {
        try {
            // Connect to the database using PDO
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Query to check if the email exists in the database
            $stmt = $pdo->prepare("SELECT user_id FROM AfriqueBotique_Users WHERE email = ?");
            $stmt->execute([$email]);

            // If the email exists, redirect to the page with security questions
            if ($stmt->rowCount() > 0) {
                // Redirect to the next page for security questions
                $_SESSION['reset_email'] = $email;  // Store email in session for later use
                header("Location: security_questions.php"); // Change to your page for security questions
                exit();
            } else {
                echo "<script>alert('Email not found. Please check your email and try again.'); window.history.back();</script>";
            }

        } catch (PDOException $e) {
            echo "<script>alert('Error verifying email. Please try again later.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Please enter a valid email.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <style>
        <?php include '../assets/css/forgotpass.css'; ?>
    </style>
</head>
<body>
	<div class="row">
		<h1>Forgot Password</h1>
		<h6 class="information-text">Enter your registered email to reset your password.</h6>
		<form action="" method="POST">
            <div class="form-group">
                <input type="email" name="email" id="user_email" required>
                <label for="user_email">Email</label>
                <button type="submit" onclick="showSpinner()">Reset Password</button>
            </div>
        </form>
		<div class="footer">
			<h5>New here? <a href="signup.php">Sign Up.</a></h5>
			<h5>Already have an account? <a href="login.php">Sign In.</a></h5>
			<p class="information-text"></p>
		</div>
	</div>
</body>
</html>
