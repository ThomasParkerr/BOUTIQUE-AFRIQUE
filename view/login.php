<?php
session_start(); // Start the session if not already started
include('../db/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Query to check user credentials
        $sql = 'SELECT * FROM AfriqueBotique_Users WHERE username = :username';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Successful login: Store user data in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];

            // Redirect to the homepage (after session setup)
            echo '<script>
                alert("Login successful! Welcome, ' . htmlspecialchars($user['first_name']) . '!");
                window.location.href = "homepage.php";
            </script>';
            exit; // Prevent further execution
        } else {
            // Invalid credentials
            echo '<script>alert("Invalid username or password.");</script>';
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

// Debug session data (for testing purposes)
if (isset($_SESSION['user_id'])) {
    echo '<script>console.log("Session Data: ' . json_encode($_SESSION) . '");</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotiqueAfrique Login Page</title>
    <!-- Include the external stylesheet -->
    <link rel="stylesheet" href="../assets/css/Newlogin.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
</head>
<body>

<div class="container">
    <!-- Left Half: Image Section -->
    <div class="image-section"></div>

    <!-- Right Half: Login Section -->
    <div class="login-section">
        <h1>Welcome Back!</h1>
        <form method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
                <span id="username-error" class="error-message"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <span id="password-error" class="error-message"></span>
            </div>
            <button type="submit" name="login" class="login-button">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        <p>Forgot Password? <a href="password_reset.php">Click Here</a></p>
        <p>Admin? <a href="admin/adminlogin.php">Admin Login</a></p>
    </div>
</div>

<script>
    function validateForm() {
        document.getElementById('username-error').textContent = '';
        document.getElementById('password-error').textContent = '';

        let isValid = true;

        // Validate Username
        const username = document.getElementById('username').value.trim();
        if (username === "") {
            document.getElementById('username-error').textContent = "Username is required";
            isValid = false;
        }

        // Validate Password
        const password = document.getElementById('password').value.trim();
        if (password === "") {
            document.getElementById('password-error').textContent = "Password is required";
            isValid = false;
        }

        return isValid; // Prevent form submission if validation fails
    }
</script>
</body>
</html>
