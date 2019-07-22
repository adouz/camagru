<?php

session_start();

// unset all SESSION VARSS
$_SESSION = array();
 
// Destroy the session
session_destroy();
 
// Redirect to login page
header("location: login.php");
exit();
?>