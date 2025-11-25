<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

try {
    $is_admin = isset($_SESSION['admin_id']);
    $content_type = $_GET['content_type'] ?? '';
    
    $sql = "SELECT cpd_id, title, description, content_type, file_path, link_url, date_created, created_at, updated_at, is_active 
            FROM cpd_content";
    
    $params = [];
    if (!$is_admin) {
        $sql .= " WHERE is_active = TRUE";
    }
    
    if (!empty($content_type)) {
        $sql .= ($is_admin ? " WHERE" : " AND") . " content_type = :content_type";
        $params[':content_type'] = $content_type;
    }
    
    $sql .= " ORDER BY date_created DESC, created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $cpd = $stmt->fetchAll();
    
    $base_path = '/internal-education-worker-report/';
    foreach ($cpd as &$item) {
        if ($item['file_path']) {
            $file_path = ltrim($item['file_path'], '/');
            if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                $item['file_url'] = $base_path . ltrim($file_path, '/');
            } else {
                $item['file_url'] = '/' . ltrim($file_path, '/');
            }
        } else {
            $item['file_url'] = null;
        }
    }
    
    echo json_encode($cpd, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

