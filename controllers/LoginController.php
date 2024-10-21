<?php
session_start(); // Start the session

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files for database connection
include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validate inputs
        if (empty($email) || empty($password)) {
            echo json_encode(['error' => 'Email and password are required.']);
            exit();
        }

        // Prepare and execute the query
        $stmt = $connection->prepare("SELECT * FROM applicants WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                echo json_encode(['error' => 'Invalid email or password.']);
                exit();
            }

            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                echo json_encode(['message' => 'Login successful!']);
            } else {
                echo json_encode(['error' => 'Invalid email or password.']);
            }
        } else {
            echo json_encode(['error' => 'Database query failed: ' . $stmt->error]);
        }
        exit();
    } else {
        echo json_encode(['error' => 'Invalid action.']);
        exit();
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
    exit();
}
?>
