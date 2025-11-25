<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Only administrators can add teacher college data'], JSON_UNESCAPED_UNICODE);
    exit;
}

$college_name = $_POST['college_name'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$file_path = $_POST['file_path'] ?? null;
$date_created = $_POST['date_created'] ?? date('Y-m-d');

if (empty($college_name) || empty($title)) {
    http_response_code(400);
    echo json_encode(['error' => 'College name and title are required'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "INSERT INTO teacher_colleges (college_name, title, description, file_path, date_created, created_by) 
            VALUES (:college_name, :title, :description, :file_path, :date_created, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':college_name' => $college_name,
        ':title' => $title,
        ':description' => $description,
        ':file_path' => $file_path,
        ':date_created' => $date_created
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Teacher college data added successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

