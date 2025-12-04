<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Function to get the base path dynamically
function getBasePath() {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $request_uri = $_SERVER['REQUEST_URI'];
    $base_path = str_replace(basename($script_name), '', $script_name);
    // If the request URI contains the base path, use it
    if (strpos($request_uri, $base_path) === 0) {
        return $base_path;
    }
    // Fallback for different server configurations (e.g., XAMPP in htdocs/ips)
    // This attempts to find the segment before 'backend/'
    $parts = explode('/backend/', $request_uri);
    if (count($parts) > 1) {
        return $parts[0] . '/';
    }
    return '/'; // Default to root
}

// Helper function to get correct file URL
function getFileUrl($file_path) {
    if (!$file_path) return null;
    
    // If already a full URL, return as is
    if (strpos($file_path, 'http') === 0) {
        return $file_path;
    }
    
    // Remove any ../ prefixes
    $cleanPath = str_replace('../', '', $file_path);
    $cleanPath = ltrim($cleanPath, '/');
    
    // Prepend base path
    return getBasePath() . $cleanPath;
}

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
    
    foreach ($provinces as &$province) {
        $province['file_url'] = getFileUrl($province['file_path']);
    }
    
    echo json_encode($provinces, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

