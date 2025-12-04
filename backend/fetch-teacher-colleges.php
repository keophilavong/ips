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
    
    foreach ($colleges as &$college) {
        $college['file_url'] = getFileUrl($college['file_path']);
    }
    
    echo json_encode($colleges, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

