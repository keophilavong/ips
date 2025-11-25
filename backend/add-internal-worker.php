<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Only administrators can add internal worker data'], JSON_UNESCAPED_UNICODE);
    exit;
}

$worker_name = $_POST['worker_name'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$file_path = $_POST['file_path'] ?? null;
$date_created = $_POST['date_created'] ?? date('Y-m-d');

if (empty($worker_name) || empty($title)) {
    http_response_code(400);
    echo json_encode(['error' => 'Worker name and title are required'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "INSERT INTO internal_workers (worker_name, title, description, file_path, date_created, created_by) 
            VALUES (:worker_name, :title, :description, :file_path, :date_created, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':worker_name' => $worker_name,
        ':title' => $title,
        ':description' => $description,
        ':file_path' => $file_path,
        ':date_created' => $date_created
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Internal worker data added successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

