<?php
session_start();
include("../db/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        // Sanitize inputs
        $currentPassword = isset($_POST['currentPassword']) ? trim($_POST['currentPassword']) : '';
        $newPassword = isset($_POST['newPassword']) ? trim($_POST['newPassword']) : '';
        $confirmPassword = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : '';

        // Validate input fields
        if (empty($currentPassword)) {
            echo "<script>alert('Current password is required.'); window.history.back();</script>";
            exit();
        }

        if (empty($newPassword)) {
            echo "<script>alert('New password is required.'); window.history.back();</script>";
            exit();
        }

        if (empty($confirmPassword)) {
            echo "<script>alert('Please confirm your new password.'); window.history.back();</script>";
            exit();
        }

        // Check if new passwords match
        if ($newPassword !== $confirmPassword) {
            echo "<script>alert('New passwords do not match.'); window.history.back();</script>";
            exit();
        }

        // Validate new password strength (at least 8 characters, one number, one letter)
        if (strlen($newPassword) < 8) {
            echo "<script>alert('New password must be at least 8 characters long.'); window.history.back();</script>";
            exit();
        }

        if (!preg_match("/[A-Za-z]/", $newPassword) || !preg_match("/[0-9]/", $newPassword)) {
            echo "<script>alert('New password must contain at least one letter and one number.'); window.history.back();</script>";
            exit();
        }

        // Fetch the current password from the database using PDO
        $stmt = $pdo->prepare("SELECT password FROM AfriqueBotique_Users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $hashedPassword = $stmt->fetchColumn();

        // Verify the current password
        if (!password_verify($currentPassword, $hashedPassword)) {
            echo "<script>alert('Current password is incorrect.'); window.history.back();</script>";
            exit();
        }

        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = $pdo->prepare("UPDATE AfriqueBotique_Users SET password = :password WHERE user_id = :user_id");
        $stmt->bindParam(':password', $hashedNewPassword, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Password updated successfully!'); window.location.href='../view/useraccount.php';</script>";
        } else {
            echo "<script>alert('Error updating password. Please try again later.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('You are not logged in. Please log in to continue.'); window.location.href='../view/login.php';</script>";
    }
}

?>
