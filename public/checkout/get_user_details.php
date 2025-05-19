<?php
session_start();
require_once '../../includes/database/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Fetch user details from database
$stmt = $pdo->prepare("SELECT name, email, phone, address FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user is found
if ($user) {
    echo json_encode([
        'success' => true,
        'full_name' => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'address' => $user['address']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
}
exit;
