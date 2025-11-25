<?php
session_start();
include "db.php";

// Check if user is admin - only admins can update documents
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo "error: Only administrators can update documents";
    exit;
}

$report_id = $_POST['report_id'];
$category = $_POST['category'];
$title = $_POST['title'];
$description = $_POST['description'];

try {
    $sql = "UPDATE reports SET category = :category, title = :title, description = :description WHERE report_id = :report_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':category' => $category,
        ':title' => $title,
        ':description' => $description,
        ':report_id' => $report_id
    ]);
    echo "success";
} catch(PDOException $e) {
    echo "error: " . $e->getMessage();
}
?>

