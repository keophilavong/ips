<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

try {
    $is_admin = isset($_SESSION['admin_id']);
    $province_name = $_GET['province_name'] ?? '';
    
    $sql = "SELECT province_id, province_name, title, description, file_path, link_url, date_created, created_at, updated_at, is_active 
            FROM provinces";
    
    $params = [];
    if (!$is_admin) {
        $sql .= " WHERE is_active = TRUE";
    }
    
    if (!empty($province_name) && $province_name !== 'all') {
        $sql .= ($is_admin ? " WHERE" : " AND") . " province_name = :province_name";
        $params[':province_name'] = $province_name;
    }
    
    $sql .= " ORDER BY date_created DESC, created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $provinces = $stmt->fetchAll();
    
    $base_path = '/internal-education-worker-report/';
    foreach ($provinces as &$province) {
        if ($province['file_path']) {
            $file_path = ltrim($province['file_path'], '/');
            if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                $province['file_url'] = $base_path . ltrim($file_path, '/');
            } else {
                $province['file_url'] = '/' . ltrim($file_path, '/');
            }
        } else {
            $province['file_url'] = null;
        }
    }
    
    echo json_encode($provinces, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

