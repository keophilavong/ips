<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

try {
    $is_admin = isset($_SESSION['admin_id']);
    $district_name = $_GET['district_name'] ?? '';
    
    $sql = "SELECT district_id, district_name, title, description, file_path, link_url, date_created, created_at, updated_at, is_active 
            FROM districts";
    
    $params = [];
    if (!$is_admin) {
        $sql .= " WHERE is_active = TRUE";
    }
    
    if (!empty($district_name) && $district_name !== 'all') {
        $sql .= ($is_admin ? " WHERE" : " AND") . " district_name = :district_name";
        $params[':district_name'] = $district_name;
    }
    
    $sql .= " ORDER BY date_created DESC, created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $districts = $stmt->fetchAll();
    
    $base_path = '/internal-education-worker-report/';
    foreach ($districts as &$district) {
        if ($district['file_path']) {
            $file_path = ltrim($district['file_path'], '/');
            if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                $district['file_url'] = $base_path . ltrim($file_path, '/');
            } else {
                $district['file_url'] = '/' . ltrim($file_path, '/');
            }
        } else {
            $district['file_url'] = null;
        }
    }
    
    echo json_encode($districts, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

