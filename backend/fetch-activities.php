<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

try {
    // If admin, show all activities; otherwise only active ones
    $is_admin = isset($_SESSION['admin_id']);
    
    // Get category filter from query parameter
    $category = isset($_GET['category']) ? trim($_GET['category']) : null;
    
    // Build SQL query with category filter if provided
    if ($is_admin) {
        $sql = "SELECT activity_id, title, description, image_path, video_url, document_path, category, date_created, created_at, updated_at, is_active 
                FROM activities";
        
        if ($category) {
            $sql .= " WHERE category = :category";
        }
        
        $sql .= " ORDER BY date_created DESC, created_at DESC";
    } else {
        $sql = "SELECT activity_id, title, description, image_path, video_url, document_path, category, date_created, created_at, updated_at 
                FROM activities 
                WHERE is_active = TRUE";
        
        if ($category) {
            $sql .= " AND category = :category";
        }
        
        $sql .= " ORDER BY date_created DESC, created_at DESC";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($category) {
        $stmt->execute([':category' => $category]);
    } else {
        $stmt->execute();
    }
    
    $activities = $stmt->fetchAll();
    
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
        
        // Detect base path from request URI (more reliable)
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Extract base path from script name (e.g., /ips/backend/fetch-activities.php -> /ips)
        $base_path = '';
        if ($script_name) {
            $parts = explode('/', trim($script_name, '/'));
            if (count($parts) > 1 && $parts[0] !== 'backend') {
                $base_path = '/' . $parts[0];
            }
        }
        
        // If we couldn't detect from script, try from request URI
        if (!$base_path && $request_uri) {
            $uri_parts = explode('/', trim($request_uri, '/'));
            if (count($uri_parts) > 0 && $uri_parts[0] !== 'backend') {
                $base_path = '/' . $uri_parts[0];
            }
        }
        
        // If base_path is not root, prepend it
        if ($base_path && $base_path !== '/') {
            return $base_path . '/' . $cleanPath;
        }
        
        // Return absolute path from web root
        return '/' . $cleanPath;
    }
    
    foreach ($activities as &$activity) {
        // Handle image path
        $activity['image_url'] = getFileUrl($activity['image_path']);
        
        // Handle document path
        $activity['document_url'] = getFileUrl($activity['document_path']);
        
        // video_url is already a URL, no conversion needed
    }
    
    echo json_encode($activities, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

