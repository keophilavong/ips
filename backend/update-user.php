<?php
session_start();
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_POST['user_id'] ?? 0;
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$role = $_POST['role'] ?? 'user';

try {
    $sql = "UPDATE users 
            SET fullname = :fullname, email = :email, role = :role 
            WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':fullname' => $fullname,
        ':email' => $email,
        ':role' => $role,
        ':user_id' => $user_id
    ]);
    
    echo json_encode(['success' => true, 'message' => 'User updated successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

