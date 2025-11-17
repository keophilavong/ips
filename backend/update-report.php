<?php
include "db.php";

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

