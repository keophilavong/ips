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

try {
    // Don't allow deleting yourself
    if ($user_id == $_SESSION['admin_id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Cannot delete your own account']);
        exit;
    }
    
    $sql = "DELETE FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    
    echo json_encode(['success' => true, 'message' => 'User deleted successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

