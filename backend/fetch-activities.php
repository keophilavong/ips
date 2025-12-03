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
    
    // Convert paths to URLs
    // Get the base path of the project
    $base_path = '/internal-education-worker-report/';
    
    foreach ($activities as &$activity) {
        // Handle image path
        if ($activity['image_path']) {
            $image_path = ltrim($activity['image_path'], '/');
            if (strpos($image_path, 'internal-education-worker-report') !== 0) {
                $activity['image_url'] = $base_path . ltrim($image_path, '/');
            } else {
                $activity['image_url'] = '/' . ltrim($image_path, '/');
            }
        } else {
            $activity['image_url'] = null;
        }
        
        // Handle document path
        if ($activity['document_path']) {
            $doc_path = ltrim($activity['document_path'], '/');
            if (strpos($doc_path, 'internal-education-worker-report') !== 0) {
                $activity['document_url'] = $base_path . ltrim($doc_path, '/');
            } else {
                $activity['document_url'] = '/' . ltrim($doc_path, '/');
            }
        } else {
            $activity['document_url'] = null;
        }
        
        // video_url is already a URL, no conversion needed
    }
    
    echo json_encode($activities, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

