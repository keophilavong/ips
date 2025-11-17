<?php
session_start();
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$activity_id = $_POST['activity_id'] ?? 0;

try {
    // Get image path before deleting
    $sql = "SELECT image_path FROM activities WHERE activity_id = :activity_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':activity_id' => $activity_id]);
    $activity = $stmt->fetch();
    
    // Delete image file if exists
    if ($activity && $activity['image_path'] && file_exists('../' . $activity['image_path'])) {
        unlink('../' . $activity['image_path']);
    }
    
    // Soft delete (set is_active to false) or hard delete
    // Using soft delete for safety
    $sql = "UPDATE activities SET is_active = FALSE WHERE activity_id = :activity_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':activity_id' => $activity_id]);
    
    echo json_encode(['success' => true, 'message' => 'Activity deleted successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

