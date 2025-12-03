<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$activity_id = $_POST['activity_id'] ?? 0;
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$date_created = $_POST['date_created'] ?? date('Y-m-d');
$video_url = trim($_POST['video_url'] ?? '');
$category = trim($_POST['category'] ?? '');

try {
    // Get old activity data
    $sql = "SELECT image_path, document_path FROM activities WHERE activity_id = :activity_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':activity_id' => $activity_id]);
    $old_activity = $stmt->fetch();
    
    // Check if new image is uploaded
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if exists
        if ($old_activity && $old_activity['image_path'] && file_exists('../' . $old_activity['image_path'])) {
            unlink('../' . $old_activity['image_path']);
        }
        
        // Upload new image
        $target_dir = "../uploads/activities/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $filename = time() . '_' . uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'uploads/activities/' . $filename;
            }
        }
    } else {
        // Keep old image path if not uploading new one
        $image_path = $old_activity['image_path'] ?? null;
    }
    
    // Check if new document is uploaded
    $document_path = null;
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        // Delete old document if exists
        if ($old_activity && $old_activity['document_path'] && file_exists('../' . $old_activity['document_path'])) {
            unlink('../' . $old_activity['document_path']);
        }
        
        // Upload new document
        $target_dir = "../uploads/activities/documents/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        $max_file_size = 100 * 1024 * 1024; // 100MB
        
        if (in_array($file_extension, $allowed_extensions) && $_FILES['document']['size'] <= $max_file_size) {
            $filename = time() . '_' . uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES['document']['tmp_name'], $target_file)) {
                $document_path = 'uploads/activities/documents/' . $filename;
            }
        }
    } else {
        // Keep old document path if not uploading new one
        $document_path = $old_activity['document_path'] ?? null;
    }
    
    // Update activity with all fields
    $sql = "UPDATE activities 
            SET title = :title, description = :description, image_path = :image_path, 
                video_url = :video_url, document_path = :document_path, category = :category,
                date_created = :date_created, updated_at = CURRENT_TIMESTAMP 
            WHERE activity_id = :activity_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':image_path' => $image_path,
        ':video_url' => $video_url ? $video_url : null,
        ':document_path' => $document_path,
        ':category' => $category ? $category : null,
        ':date_created' => $date_created,
        ':activity_id' => $activity_id
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Activity updated successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

