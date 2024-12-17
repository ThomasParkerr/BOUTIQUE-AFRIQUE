<?php
session_start();
include("../db/database.php"); // Make sure your database connection is correct

// Check if the user is coming from the email verification page
if (!isset($_SESSION['reset_email'])) {
    echo "<script>alert('Session expired. Please try again.'); window.location.href='password_reset.php';</script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve answers
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';

    // Check if all fields are filled
    if (empty($lastName) || empty($address) || empty($city)) {
        echo "<script>alert('Please answer all the security questions.'); window.history.back();</script>";
        exit();
    }

    // Retrieve the stored answers based on the email from the session
    try {
        // Query to get the user's security answers from the `AfriqueBotique_Users` table and city from `AfriqueBotique_payments`
        $stmt = $pdo->prepare("SELECT u.last_name, p.address, p.city AS city 
                               FROM AfriqueBotique_Users u
                               LEFT JOIN AfriqueBotique_payments p ON u.user_id = p.user_id
                               WHERE u.email = ?");
        $stmt->execute([$_SESSION['reset_email']]);

        // Check if the user exists in the database
        if ($stmt->rowCount() > 0) {
            // Fetch the user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Validate answers: Compare answers with user details and payment city
            if ($lastName === $user['last_name'] && $address === $user['address'] && $city === $user['city']) {
                // Answers are correct, allow password reset
                header("Location: reset_password.php"); // Redirect to reset password page
                exit();
            } else {
                echo "<script>alert('Security answers are incorrect. Please try again.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('User not found. Please try again.'); window.history.back();</script>";
        }

    } catch (PDOException $e) {
        echo "<script>alert('Error verifying answers. Please try again later.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Questions</title>
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <style>
        <?php include '../assets/css/forgotpass.css'; ?>
    </style>
</head>
<body>
    <div class="row">
        <h1>Security Questions</h1>
        <h6 class="information-text">Answer the following questions to verify your identity.</h6>
        <form action="" method="POST">
            <div class="form-group">
                <label for="last_name">What is your last name?</label>
                <input type="text" name="last_name" id="last_name" required>
            </div>
            <div class="form-group">
                <label for="address">What is your address?</label>
                <input type="text" name="address" id="address" required>
            </div>
            <div class="form-group">
                <label for="city">What is the name of your city?</label>
                <input type="text" name="city" id="city" required>
            </div>
            <button type="submit">Submit</button>
        </form>
        <div class="footer">
            <h5>New here? <a href="signup.php">Sign Up.</a></h5>
            <h5>Already have an account? <a href="login.php">Sign In.</a></h5>
            <p class="information-text"></p>
        </div>
    </div>
</body>
</html>
