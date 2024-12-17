<?php
session_start(); // Start the session if not already started
include('../../db/database.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']); // Get the username from the form
    $password = trim($_POST['password']); // Get the password from the form

    try {
        // Query to check admin credentials
        $sql = 'SELECT * FROM AfriqueBotique_Admin WHERE username = :username AND role = "SuperAdmin"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validate credentials
        if ($admin && $password === $admin['password']) {
            // Successful login: Store admin data in session
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = $admin['role'];

            // Redirect to the admin dashboard
            echo '<script>
                alert("Admin Login successful! Welcome, ' . htmlspecialchars($admin['username']) . '!");
                window.location.href = "dashboard.php";
            </script>';
            exit; // Prevent further execution
        } else {
            // Invalid credentials or not an admin
            echo '<script>alert("Invalid admin username or password.");</script>';
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo '<script>alert("Database error: ' . $e->getMessage() . '");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BotiqueAfrique</title>
    <!-- Include the external stylesheet -->
    <link rel="stylesheet" href="../../assets/css/Newlogin.css">
    <link rel="icon" href="../../logo.png" type="image/x-icon">
</head>
<body>
<div class="container">
    <!-- Left Half: Image Section -->
    <div class="image-section"></div>

    <!-- Right Half: Login Section -->
    <div class="login-section">
        <h1>Admin Portal</h1>
        <form method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
                <span id="username-error" class="error-message"></span>
            </div>
            <div class="form-group">
                <label for="password">Admin Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <span id="password-error" class="error-message"></span>
            </div>
            <button type="submit" name="login" class="login-button">Login</button>
        </form>
        <p>Back to regular Login Page! <a href="../login.php">Click Here</a></p>
    </div>
</div>

<script>
    // Form validation before submission
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
