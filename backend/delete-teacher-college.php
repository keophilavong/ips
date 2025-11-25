<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Only administrators can delete teacher college data'], JSON_UNESCAPED_UNICODE);
    exit;
}

$college_id = $_POST['college_id'] ?? 0;

try {
    // Get file path before deleting
    $sql = "SELECT file_path FROM teacher_colleges WHERE college_id = :college_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':college_id' => $college_id]);
    $row = $stmt->fetch();
    
    if ($row && $row['file_path'] && file_exists($row['file_path'])) {
        unlink($row['file_path']);
    }
    
    // Delete from database
    $sql = "DELETE FROM teacher_colleges WHERE college_id = :college_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':college_id' => $college_id]);
    
    echo json_encode(['success' => true, 'message' => 'Teacher college data deleted successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

