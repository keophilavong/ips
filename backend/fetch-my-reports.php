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
    $sql = "SELECT report_id, category, title, description, file_path, created_at 
            FROM reports 
            WHERE user_id = :user_id
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $reports = $stmt->fetchAll();
    
    // Convert file paths to URLs
    foreach ($reports as &$report) {
        $report['file_url'] = getFileUrl($report['file_path']);
    }
    
    echo json_encode($reports, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

