<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "SELECT report_id, category, title, description, file_path, created_at 
            FROM reports 
            WHERE user_id = :user_id
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
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
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

