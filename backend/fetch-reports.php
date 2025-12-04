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
        
        // Extract base path from script name (e.g., /ips/backend/fetch-reports.php -> /ips)
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
        
        // If path already starts with files/, use it directly
        if (strpos($cleanPath, 'files/') === 0) {
            if ($base_path && $base_path !== '/') {
                return $base_path . '/' . $cleanPath;
            }
            return '/' . $cleanPath;
        }
        
        // Otherwise, assume it's in files/ directory
        if ($base_path && $base_path !== '/') {
            return $base_path . '/files/' . $cleanPath;
        }
        return '/files/' . $cleanPath;
    }
    
    // Convert file paths to URLs
    foreach ($reports as &$report) {
        $report['file_url'] = getFileUrl($report['file_path']);
    }
    
    echo json_encode($reports, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

