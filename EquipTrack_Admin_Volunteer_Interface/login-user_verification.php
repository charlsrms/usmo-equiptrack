<?php
session_start(); // This should be at the top of your file
require 'db.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User not logged in, redirect to login page
    header('Location: index.php');
    exit;
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];
?>
