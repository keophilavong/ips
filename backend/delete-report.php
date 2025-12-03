<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Check if user is admin - only admins can delete documents
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດລຶບເອກະສານ'], JSON_UNESCAPED_UNICODE);
    exit;
}

$report_id = $_POST['report_id'] ?? 0;

if (empty($report_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID ລາຍງານຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Get file path before deleting
    $sql = "SELECT file_path FROM reports WHERE report_id = :report_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':report_id' => $report_id]);
    $row = $stmt->fetch();
    
    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'ບໍ່ພົບລາຍງານ'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Delete file if exists
    if ($row['file_path']) {
        $file_path = '../' . ltrim($row['file_path'], '/');
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete from database
    $sql = "DELETE FROM reports WHERE report_id = :report_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':report_id' => $report_id]);
    
    echo json_encode(['success' => true, 'message' => 'ລຶບລາຍງານສຳເລັດແລ້ວ'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

