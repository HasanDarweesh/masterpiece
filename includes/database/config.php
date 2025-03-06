<?php
$host = 'localhost';  // Database host (e.g., 127.0.0.1)
$dbname = 'ecommarce_pro';  // Your database name
$username = 'root';  // Your database username
$password = '';  // Your database password

try {
    // Create a PDO instance (connection)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Set PDO to throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected successfully!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
