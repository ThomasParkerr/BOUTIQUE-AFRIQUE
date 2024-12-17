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

        // Validate current password
        if (empty($currentPassword)) {
            echo "<script>alert('Current password is required.'); window.history.back();</script>";
            exit();
        }

        // Check if new passwords match
        if ($newPassword !== $confirmPassword) {
            echo "<script>alert('New passwords do not match.'); window.history.back();</script>";
            exit();
        }

        // Validate new password strength
        if (strlen($newPassword) < 8) {
            echo "<script>alert('New password must be at least 8 characters long.'); window.history.back();</script>";
            exit();
        }

        try {
            // Use PDO to connect to the database
            $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fetch the current password from the database using PDO
            $stmt = $pdo->prepare("SELECT password FROM team_project_users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $hashedPassword = $stmt->fetchColumn();

            // Verify the current password
            if (!$hashedPassword || !password_verify($currentPassword, $hashedPassword)) {
                echo "<script>alert('Current password is incorrect.'); window.history.back();</script>";
                exit();
            }

            // Hash the new password
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $stmt = $pdo->prepare("UPDATE team_project_users SET password = ? WHERE user_id = ?");
            $stmt->execute([$hashedNewPassword, $userId]);

            // Redirect to intermediary page
            echo "<script>alert('Password updated successfully!'); window.location.href='../view/intermediary_page.php';</script>";

        } catch (PDOException $e) {
            echo "<script>alert('Error updating password. Please try again later.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('You are not logged in. Please log in to continue.'); window.location.href='../view/login.php';</script>";
    }
}

?>
