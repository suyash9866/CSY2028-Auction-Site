<?php
session_start(); 

session_unset(); // Remove session variables 
session_destroy(); // Destroy session 

// redirect to login
header("Location: login.php");
exit;
?>
