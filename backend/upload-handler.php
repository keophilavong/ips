<?php
session_start();
include "db.php";

// Check if user is admin - only admins can upload documents
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo "error: Only administrators can upload documents";
    exit;
}

// Allowed file extensions
$allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
$max_file_size = 100 * 1024 * 1024; // 100MB

// Get form data
$category = $_POST['category'] ?? $_POST['college'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$date = $_POST['date'] ?? date('Y-m-d');

// Validate required fields
if (empty($category) || empty($title)) {
    echo "error: College and title are required";
    exit;
}

// Create upload directory if it doesn't exist
$target_dir = "../files/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$uploaded_files = [];
$errors = [];

// Handle multiple files (file_0, file_1, etc.)
$file_index = 0;
while (isset($_FILES["file_$file_index"])) {
    $file = $_FILES["file_$file_index"];
    
    if ($file["error"] === UPLOAD_ERR_OK) {
        $filename = basename($file["name"]);
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $file_size = $file["size"];
        
        // Validate file extension
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "File '{$filename}': File type '" . strtoupper($file_extension) . "' is not allowed";
            $file_index++;
            continue;
        }
        
        // Validate file size
        if ($file_size > $max_file_size) {
            $errors[] = "File '{$filename}': File size exceeds the maximum allowed size of 100MB";
            $file_index++;
            continue;
        }
        
        // Generate unique filename
        $unique_filename = time() . "_" . uniqid() . "_" . $file_index . "_" . $filename;
        $target_file = $target_dir . $unique_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $uploaded_files[] = [
                'original_name' => $filename,
                'stored_path' => $target_file,
                'size' => $file_size
            ];
        } else {
            $errors[] = "File '{$filename}': Failed to upload. Please check file permissions.";
        }
    } else if ($file["error"] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "File upload error for file index $file_index";
    }
    
    $file_index++;
}

// Also check for single file upload (backward compatibility)
if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
    $file = $_FILES["file"];
    $filename = basename($file["name"]);
    $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $file_size = $file["size"];
    
    // Validate file extension
    if (in_array($file_extension, $allowed_extensions)) {
        // Validate file size
        if ($file_size <= $max_file_size) {
            // Generate unique filename
            $unique_filename = time() . "_" . uniqid() . "_" . $filename;
            $target_file = $target_dir . $unique_filename;
            
            // Move uploaded file
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $uploaded_files[] = [
                    'original_name' => $filename,
                    'stored_path' => $target_file,
                    'size' => $file_size
                ];
            } else {
                $errors[] = "File '{$filename}': Failed to upload. Please check file permissions.";
            }
        } else {
            $errors[] = "File '{$filename}': File size exceeds the maximum allowed size of 100MB";
        }
    } else {
        $errors[] = "File '{$filename}': File type '" . strtoupper($file_extension) . "' is not allowed";
    }
}

// If no files were uploaded
if (empty($uploaded_files)) {
    if (!empty($errors)) {
        echo "error: " . implode("; ", $errors);
    } else {
        echo "error: No files were uploaded";
    }
    exit;
}

// If there are errors but some files uploaded, show warnings but continue
if (!empty($errors)) {
    // Log errors but continue with successful uploads
    error_log("Upload warnings: " . implode("; ", $errors));
}

// Insert each file into database
$success_count = 0;
$failed_files = [];

// Documents uploaded by admin - set user_id to null (admin-only documents)
$user_id = null;

foreach ($uploaded_files as $file_info) {
    try {
        // Use created_at (timestamp) instead of date_created
        // The date field from form can be stored in description or we can add it to title
        $sql = "INSERT INTO reports (category, title, description, file_path, user_id) 
                VALUES (:category, :title, :description, :file_path, :user_id)";
        $stmt = $conn->prepare($sql);
        $description_text = $description;
        if (!empty($date)) {
            $description_text = ($description ? $description . "\n\n" : '') . "Date: " . $date;
        }
        $stmt->execute([
            ':category' => $category,
            ':title' => $title . (count($uploaded_files) > 1 ? ' (' . $file_info['original_name'] . ')' : ''),
            ':description' => $description_text,
            ':file_path' => $file_info['stored_path'],
            ':user_id' => $user_id
        ]);
        $success_count++;
    } catch(PDOException $e) {
        // Delete uploaded file if database insert fails
        if (file_exists($file_info['stored_path'])) {
            unlink($file_info['stored_path']);
        }
        $failed_files[] = $file_info['original_name'] . ': ' . $e->getMessage();
    }
}

if ($success_count > 0) {
    if (!empty($failed_files)) {
        echo "partial_success: {$success_count} file(s) uploaded successfully. Failed: " . implode("; ", $failed_files);
    } else {
        echo "success";
    }
} else {
    echo "error: Failed to save files to database. " . (!empty($failed_files) ? implode("; ", $failed_files) : '');
}
?>
