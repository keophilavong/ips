<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized. Please login as admin.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validate required fields
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$date_created = $_POST['date_created'] ?? date('Y-m-d');
$video_url = trim($_POST['video_url'] ?? '');
$category = trim($_POST['category'] ?? '');

if (empty($title)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Title is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($description)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Description is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Handle image upload
$image_path = null;
$image_error = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/activities/";
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $image_error = 'Failed to create upload directory';
            }
        }
        
        if (!$image_error) {
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $max_file_size = 10 * 1024 * 1024; // 10MB
            
            if (!in_array($file_extension, $allowed_extensions)) {
                $image_error = 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF, WEBP';
            } elseif ($_FILES['image']['size'] > $max_file_size) {
                $image_error = 'File size exceeds 10MB limit';
            } else {
                $filename = time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = 'uploads/activities/' . $filename;
                } else {
                    $image_error = 'Failed to upload image file';
                }
            }
        }
    } else {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $image_error = $upload_errors[$_FILES['image']['error']] ?? 'Unknown upload error';
    }
}

// If image upload failed, return error
if ($image_error && isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Image upload failed: ' . $image_error], JSON_UNESCAPED_UNICODE);
    exit;
}

// Handle document upload
$document_path = null;
$document_error = null;

if (isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/activities/documents/";
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $document_error = 'Failed to create upload directory';
            }
        }
        
        if (!$document_error) {
            $file_extension = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
            $max_file_size = 100 * 1024 * 1024; // 100MB
            
            if (!in_array($file_extension, $allowed_extensions)) {
                $document_error = 'Invalid file type. Allowed: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT';
            } elseif ($_FILES['document']['size'] > $max_file_size) {
                $document_error = 'File size exceeds 100MB limit';
            } else {
                $filename = time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $filename;
                
                if (move_uploaded_file($_FILES['document']['tmp_name'], $target_file)) {
                    $document_path = 'uploads/activities/documents/' . $filename;
                } else {
                    $document_error = 'Failed to upload document file';
                }
            }
        }
    } else {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $document_error = $upload_errors[$_FILES['document']['error']] ?? 'Unknown upload error';
    }
}

// If document upload failed, return error
if ($document_error && isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Document upload failed: ' . $document_error], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Since admins are not in the users table, set created_by to NULL
    // The foreign key constraint allows NULL values (ON DELETE SET NULL)
    $sql = "INSERT INTO activities (title, description, image_path, video_url, document_path, category, date_created, created_by) 
            VALUES (:title, :description, :image_path, :video_url, :document_path, :category, :date_created, :created_by)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':image_path' => $image_path,
        ':video_url' => $video_url ? $video_url : null,
        ':document_path' => $document_path,
        ':category' => $category ? $category : null,
        ':date_created' => $date_created,
        ':created_by' => null  // Admins are not in users table, so set to NULL
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Activity added successfully',
        'activity_id' => $conn->lastInsertId()
    ], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

