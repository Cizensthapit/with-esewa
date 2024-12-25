<?php
// Start session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or any other page after logout
header("Location: index.php"); // Replace with the URL of your dashboard page
exit();
?>
