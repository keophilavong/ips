<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$response = [
    'logged_in' => false,
    'type' => null,
    'username' => null,
    'name' => null
];

// Check if admin is logged in
if (isset($_SESSION['admin_id'])) {
    $response['logged_in'] = true;
    $response['type'] = 'admin';
    $response['username'] = $_SESSION['admin_username'] ?? null;
    $response['name'] = $_SESSION['admin_username'] ?? 'Admin';
}

// Check if regular user is logged in
elseif (isset($_SESSION['email'])) {
    $response['logged_in'] = true;
    $response['type'] = 'user';
    $response['username'] = $_SESSION['email'] ?? null;
    $response['name'] = $_SESSION['fullname'] ?? $_SESSION['email'] ?? 'User';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>

