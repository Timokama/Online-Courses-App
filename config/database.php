<?php
// config/database.php

$host = 'localhost'; // Your database host
$dbase   = 'online_courses'; // Your database name
$user = 'root'; // Your database user
$pass = 'secret123'; // Your database password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbase;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
// Create connection
$connection = new mysqli($host, $user, $pass, $dbase);
// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
