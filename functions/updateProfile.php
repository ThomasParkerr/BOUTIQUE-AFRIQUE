<?php
session_start();
include('../db/database.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input data and sanitize
    $username = htmlspecialchars(trim($_POST['username']));
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));

    // Validate email format (basic validation)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Example of additional validation for required fields
    if (empty($username) || empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($address)) {
        die("All fields are required.");
    }

    try {
        // Prepare SQL query to update personal information in the correct table
        $query = "UPDATE AfriqueBotique_Users SET 
                  username = :username, 
                  first_name = :firstname, 
                  last_name = :lastname, 
                  email = :email, 
                  phone_number = :phone, 
                  address = :address 
                  WHERE user_id = :user_id"; // Matching the column names in your table
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'user_id' => $_SESSION['user_id'] // Assuming user_id is stored in the session
        ]);

        // Redirect with success message
        header("Location: ../view/useraccount.php?success=Profile updated successfully");
        exit();
    } catch (PDOException $e) {
        // Handle any errors that occur during the update process
        echo "Error: " . $e->getMessage();
    }
}
?>
