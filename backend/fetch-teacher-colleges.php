<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

try {
    // If admin, show all; otherwise only active ones
    $is_admin = isset($_SESSION['admin_id']);
    
    if ($is_admin) {
        $sql = "SELECT college_id, college_name, title, description, file_path, date_created, created_at, updated_at, is_active 
                FROM teacher_colleges 
                ORDER BY date_created DESC, created_at DESC";
    } else {
        $sql = "SELECT college_id, college_name, title, description, file_path, date_created, created_at, updated_at 
                FROM teacher_colleges 
                WHERE is_active = TRUE 
                ORDER BY date_created DESC, created_at DESC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $colleges = $stmt->fetchAll();
    
    // Convert file paths to URLs
    $base_path = '/internal-education-worker-report/';
    foreach ($colleges as &$college) {
        if ($college['file_path']) {
            $file_path = ltrim($college['file_path'], '/');
            if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                $college['file_url'] = $base_path . ltrim($file_path, '/');
            } else {
                $college['file_url'] = '/' . ltrim($file_path, '/');
            }
        } else {
            $college['file_url'] = null;
        }
    }
    
    echo json_encode($colleges, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

