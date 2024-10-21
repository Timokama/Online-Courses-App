<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = [];

// Destroy the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Finally, destroy the session

// Redirect to the login page or homepage
header('Location: login.php');
exit();
?>