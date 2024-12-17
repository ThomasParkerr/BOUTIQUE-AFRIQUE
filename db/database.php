<?php
// db_connect.php
$servername = 'localhost';  // Database host
$dbname = 'webtech_fall2024_thomas_parker';   // Database name
$username = 'thomas.parker';         // Database username
$password = 'Aklds1111';             // Database password

try {
   $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   die("Database connection failed: " . $e->getMessage());
}
?>