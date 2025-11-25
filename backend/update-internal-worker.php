<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Only administrators can update internal worker data'], JSON_UNESCAPED_UNICODE);
    exit;
}

$worker_id = $_POST['worker_id'] ?? 0;
$worker_name = $_POST['worker_name'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$file_path = $_POST['file_path'] ?? null;
$date_created = $_POST['date_created'] ?? null;
$is_active = isset($_POST['is_active']) ? ($_POST['is_active'] === 'true' || $_POST['is_active'] === true) : true;

if (empty($worker_name) || empty($title)) {
    http_response_code(400);
    echo json_encode(['error' => 'Worker name and title are required'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "UPDATE internal_workers 
            SET worker_name = :worker_name, title = :title, description = :description, 
                file_path = :file_path, date_created = :date_created, is_active = :is_active,
                updated_at = CURRENT_TIMESTAMP
            WHERE worker_id = :worker_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':worker_id' => $worker_id,
        ':worker_name' => $worker_name,
        ':title' => $title,
        ':description' => $description,
        ':file_path' => $file_path,
        ':date_created' => $date_created,
        ':is_active' => $is_active
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Internal worker data updated successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

