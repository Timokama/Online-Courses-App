<?php
// Start the session
session_start();
file_put_contents('php://stderr', print_r($_POST, true));


// Include the necessary files for database connection, etc.
include_once 'config/database.php'; // Make sure the path is correct
include_once 'controllers/ApplicantController.php'; // Adjust path as necessary

// Define a default action
$action = $_GET['action'] ?? 'home';

// Handle different actions based on the URL
switch ($action) {
    case 'register':
        include 'public/register.php'; // Load the registration page
        break;
    
    case 'login':
        include 'public/login.php'; // Load the login page
        break;

    case 'home':
    default:
        include 'public/index.html'; // Load the home page
        break;
}

// Example of a home view (views/home.php)
if (!file_exists('public/index.html')) {
    file_put_contents('public/index.php', '<h1>Welcome to the Online Courses</h1>');
}
?>
