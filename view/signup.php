<?php
session_start(); // Start the session if not already started
include('../db/database.php'); // Include database connection

// Initialize variables for input and error messages
$first_name = $last_name = $email = $username = "";
$firstNameError = $lastNameError = $emailError = $usernameError = $passwordError = $confirmPasswordError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Server-side validation
    $isValid = true;

    if (empty($first_name)) {
        $firstNameError = "First name is required.";
        $isValid = false;
    }

    if (empty($last_name)) {
        $lastNameError = "Last name is required.";
        $isValid = false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format.";
        $isValid = false;
    }

    if (empty($username)) {
        $usernameError = "Username is required.";
        $isValid = false;
    }

    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[^\w]/', $password)) {
        $passwordError = "Password must be at least 8 characters long, include a number, and a special character.";
        $isValid = false;
    }

    if ($password !== $confirmPassword) {
        $confirmPasswordError = "Passwords do not match.";
        $isValid = false;
    }

    if ($isValid) {
        try {
            // Check if email or username already exists
            $query = "SELECT COUNT(*) FROM AfriqueBotique_Users WHERE email = :email OR username = :username";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['email' => $email, 'username' => $username]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $_SESSION['error'] = "Email or username already exists.";
                header("Location: signup.php");
                exit();
            } else {
                // Hash the password and insert data into the database
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $query = "INSERT INTO AfriqueBotique_Users (first_name, last_name, email, username, password, date_created) 
                          VALUES (:first_name, :last_name, :email, :username, :password, NOW())";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'username' => $username,
                    'password' => $hashedPassword,
                ]);

                $_SESSION['success'] = "Account created successfully! Please <a href='login.php'>Login here</a>";
                header("Location: signup.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error occurred: " . $e->getMessage();
            header("Location: signup.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-Up Page</title>
    <link rel="stylesheet" href="../assets/css/signup.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <style>
        .error-message {
            color: red;
            font-size: 12px;
        }
        .success-message {
            color: green;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="image-section"></div>
    
    <div class="signup-section">
        <h1>Create an Account</h1>
        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-message"><?php echo $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="signup.php" method="POST">
        <div class="form-group">
    <label for="first_name">First Name</label>
    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" placeholder="Enter your first name" required>
    <span class="error-message"><?php echo $firstNameError; ?></span>
</div>
<div class="form-group">
    <label for="last_name">Last Name</label>
    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" placeholder="Enter your last name" required>
    <span class="error-message"><?php echo $lastNameError; ?></span>
</div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email" required>
                <span class="error-message"><?php echo $emailError; ?></span>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Choose a username" required>
                <span class="error-message"><?php echo $usernameError; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
                <span class="error-message"><?php echo $passwordError; ?></span>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                <span class="error-message"><?php echo $confirmPasswordError; ?></span>
            </div>
            <button type="submit" class="signup-button">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

</body>
</html>
