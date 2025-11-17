<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "SELECT user_id, fullname, email, role, created_at 
            FROM users 
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    echo json_encode($users, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

