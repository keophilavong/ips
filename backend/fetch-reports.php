<?php
header('Content-Type: application/json; charset=utf-8');
include "db.php";

try {
    $sql = "SELECT report_id, category, title, description, file_path, created_at 
            FROM reports 
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $reports = $stmt->fetchAll();
    
    // Convert file paths to URLs
    foreach ($reports as &$report) {
        if ($report['file_path']) {
            // Remove ../ if present and ensure proper path
            $report['file_url'] = '/' . ltrim(str_replace('../', '', $report['file_path']), '/');
        } else {
            $report['file_url'] = null;
        }
    }
    
    echo json_encode($reports, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

