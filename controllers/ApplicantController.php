<?php
// Include database connection
include_once '../config/database.php';

// Start session to manage error messages
session_start();

// Initialize variables
$errorMessage = '';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $errorMessage = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Invalid email format.';
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to insert user into the database
        $stmt = $connection->prepare("INSERT INTO applicants (username, email, password) VALUES (?, ?, ?)");
        
        // Bind parameters
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        // Execute the statement
        if ($stmt->execute()) {
            // Registration successful
            echo json_encode(['message' => 'Registration successful!']);
        } else {
            // Handle database insertion error
            $errorMessage = 'Database error: Unable to register user.';
        }

        // Close the statement
        $stmt->close();
    }
}

// Return error message as JSON
if (!empty($errorMessage)) {
    echo json_encode(['error' => $errorMessage]);
    exit();
}

// Close the database connection
$connection->close();
?>
