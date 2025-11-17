<?php
session_start();
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

try {
    // Check if new image is uploaded
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Get old image path
        $sql = "SELECT image_path FROM activities WHERE activity_id = :activity_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':activity_id' => $activity_id]);
        $old_activity = $stmt->fetch();
        
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
    }
    
    // Update activity
    if ($image_path) {
        $sql = "UPDATE activities 
                SET title = :title, description = :description, image_path = :image_path, 
                    date_created = :date_created, updated_at = CURRENT_TIMESTAMP 
                WHERE activity_id = :activity_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':image_path' => $image_path,
            ':date_created' => $date_created,
            ':activity_id' => $activity_id
        ]);
    } else {
        $sql = "UPDATE activities 
                SET title = :title, description = :description, date_created = :date_created, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE activity_id = :activity_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':date_created' => $date_created,
            ':activity_id' => $activity_id
        ]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Activity updated successfully'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

