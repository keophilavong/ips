<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

try {
    $is_admin = isset($_SESSION['admin_id']);
    
    if ($is_admin) {
        $sql = "SELECT worker_id, worker_name, title, description, file_path, date_created, created_at, updated_at, is_active 
                FROM internal_workers 
                ORDER BY date_created DESC, created_at DESC";
    } else {
        $sql = "SELECT worker_id, worker_name, title, description, file_path, date_created, created_at, updated_at 
                FROM internal_workers 
                WHERE is_active = TRUE 
                ORDER BY date_created DESC, created_at DESC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $workers = $stmt->fetchAll();
    
    $base_path = '/internal-education-worker-report/';
    foreach ($workers as &$worker) {
        if ($worker['file_path']) {
            $file_path = ltrim($worker['file_path'], '/');
            if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                $worker['file_url'] = $base_path . ltrim($file_path, '/');
            } else {
                $worker['file_url'] = '/' . ltrim($file_path, '/');
            }
        } else {
            $worker['file_url'] = null;
        }
    }
    
    echo json_encode($workers, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

