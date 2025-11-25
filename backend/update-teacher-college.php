<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Only administrators can update teacher college data'], JSON_UNESCAPED_UNICODE);
    exit;
}

$college_id = $_POST['college_id'] ?? 0;
$college_name = $_POST['college_name'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$file_path = $_POST['file_path'] ?? null;
$date_created = $_POST['date_created'] ?? null;
$is_active = isset($_POST['is_active']) ? ($_POST['is_active'] === 'true' || $_POST['is_active'] === true) : true;

if (empty($college_name) || empty($title)) {
    http_response_code(400);
    echo json_encode(['error' => 'College name and title are required'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "UPDATE teacher_colleges 
            SET college_name = :college_name, title = :title, description = :description, 
                file_path = :file_path, date_created = :date_created, is_active = :is_active,
                updated_at = CURRENT_TIMESTAMP
            WHERE college_id = :college_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':college_id' => $college_id,
        ':college_name' => $college_name,
        ':title' => $title,
        ':description' => $description,
        ':file_path' => $file_path,
        ':date_created' => $date_created,
        ':is_active' => $is_active
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Teacher college data updated successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

