<?php
session_start();
// Database configuration
$db_host = 'localhost';
$db_name = 'HR';
$db_user = 'root'; 
$db_pass = '';              

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Optional: Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session start (optional - for login sessions)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

