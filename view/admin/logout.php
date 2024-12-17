<?php
session_start();
session_unset(); // Unsets all session variables
session_destroy(); // Destroys the session

// Redirect to login page
header('Location: adminlogin.php');
exit();
?>
