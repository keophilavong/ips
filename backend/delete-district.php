<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Only administrators can delete district data'], JSON_UNESCAPED_UNICODE);
    exit;
}

$district_id = $_POST['district_id'] ?? 0;

try {
    $sql = "SELECT file_path FROM districts WHERE district_id = :district_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':district_id' => $district_id]);
    $row = $stmt->fetch();
    
    if ($row && $row['file_path'] && file_exists($row['file_path'])) {
        unlink($row['file_path']);
    }
    
    $sql = "DELETE FROM districts WHERE district_id = :district_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':district_id' => $district_id]);
    
    echo json_encode(['success' => true, 'message' => 'District data deleted successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

