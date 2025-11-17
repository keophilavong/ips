<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

try {
    // If admin, show all activities; otherwise only active ones
    $is_admin = isset($_SESSION['admin_id']);
    
    if ($is_admin) {
        $sql = "SELECT activity_id, title, description, image_path, date_created, created_at, updated_at, is_active 
                FROM activities 
                ORDER BY date_created DESC, created_at DESC";
    } else {
        $sql = "SELECT activity_id, title, description, image_path, date_created, created_at, updated_at 
                FROM activities 
                WHERE is_active = TRUE 
                ORDER BY date_created DESC, created_at DESC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $activities = $stmt->fetchAll();
    
    // Convert image paths to URLs
    // Get the base path of the project
    $base_path = '/internal-education-worker-report/';
    
    foreach ($activities as &$activity) {
        if ($activity['image_path']) {
            // Ensure the path starts with the project base path
            $image_path = ltrim($activity['image_path'], '/');
            // If path doesn't start with 'internal-education-worker-report', add it
            if (strpos($image_path, 'internal-education-worker-report') !== 0) {
                $activity['image_url'] = $base_path . ltrim($image_path, '/');
            } else {
                $activity['image_url'] = '/' . ltrim($image_path, '/');
            }
        } else {
            $activity['image_url'] = null;
        }
    }
    
    echo json_encode($activities, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

