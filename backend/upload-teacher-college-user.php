<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Allow both logged-in users and guests to upload
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Allowed file extensions
$allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
$max_file_size = 100 * 1024 * 1024; // 100MB

// Get form data
$category = $_POST['category'] ?? $_POST['college'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$date = $_POST['date'] ?? $_POST['date_created'] ?? date('Y-m-d');

// Validate required fields
if (empty($category) || empty($title)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ຊື່ວິທະຍາໄລຄູ ແລະ ຫົວຂໍ້ຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
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
            $errors[] = "ໄຟລ໌ '{$filename}': ປະເພດໄຟລ໌ '" . strtoupper($file_extension) . "' ບໍ່ຖືກອະນຸຍາດ";
            $file_index++;
            continue;
        }
        
        // Validate file size
        if ($file_size > $max_file_size) {
            $errors[] = "ໄຟລ໌ '{$filename}': ຂະໜາດໄຟລ໌ເກີນຂະໜາດສູງສຸດ 100MB";
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
            $errors[] = "ໄຟລ໌ '{$filename}': ບໍ່ສາມາດອັບໂຫຼດໄດ້. ກະລຸນາກວດສອບສິດການເຂົ້າເຖິງໄຟລ໌.";
        }
    } else if ($file["error"] !== UPLOAD_ERR_NO_FILE) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'ໄຟລ໌ເກີນ upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'ໄຟລ໌ເກີນ MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'ໄຟລ໌ຖືກອັບໂຫຼດບາງສ່ວນເທົ່ານັ້ນ',
            UPLOAD_ERR_NO_TMP_DIR => 'ບໍ່ພົບໂຟລ໌ເດີຊົ່ວຄາວ',
            UPLOAD_ERR_CANT_WRITE => 'ບໍ່ສາມາດຂຽນໄຟລ໌ລົງດິສ',
            UPLOAD_ERR_EXTENSION => 'ການອັບໂຫຼດໄຟລ໌ຖືກຢຸດໂດຍ extension'
        ];
        $errors[] = $upload_errors[$file["error"]] ?? 'ຂໍ້ຜິດພາດບໍ່ຮູ້ຈັກໃນການອັບໂຫຼດໄຟລ໌';
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
                $errors[] = "ໄຟລ໌ '{$filename}': ບໍ່ສາມາດອັບໂຫຼດໄດ້. ກະລຸນາກວດສອບສິດການເຂົ້າເຖິງໄຟລ໌.";
            }
        } else {
            $errors[] = "ໄຟລ໌ '{$filename}': ຂະໜາດໄຟລ໌ເກີນຂະໜາດສູງສຸດ 100MB";
        }
    } else {
        $errors[] = "ໄຟລ໌ '{$filename}': ປະເພດໄຟລ໌ '" . strtoupper($file_extension) . "' ບໍ່ຖືກອະນຸຍາດ";
    }
}

// If no files were uploaded
if (empty($uploaded_files)) {
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => implode("; ", $errors)], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ບໍ່ມີໄຟລ໌ທີ່ຖືກອັບໂຫຼດ'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// If there are errors but some files uploaded, show warnings but continue
if (!empty($errors)) {
    // Log errors but continue with successful uploads
    error_log("Upload warnings: " . implode("; ", $errors));
}

// Insert each file into database (reports table for user submissions)
$success_count = 0;
$failed_files = [];

foreach ($uploaded_files as $file_info) {
    try {
        $description_text = $description;
        if (!empty($date)) {
            $description_text = ($description ? $description . "\n\n" : '') . "ວັນທີ: " . $date;
        }
        
        // Insert into reports table with user_id (for user submissions)
        $sql = "INSERT INTO reports (category, title, description, file_path, user_id) 
                VALUES (:category, :title, :description, :file_path, :user_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':category' => $category,
            ':title' => $title . (count($uploaded_files) > 1 ? ' (' . $file_info['original_name'] . ')' : ''),
            ':description' => $description_text,
            ':file_path' => $file_info['stored_path'],
            ':user_id' => $user_id  // Can be null for guests
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
        echo json_encode([
            'success' => true, 
            'message' => "ອັບໂຫຼດ {$success_count} ໄຟລ໌ສຳເລັດ. ບໍ່ສຳເລັດ: " . implode("; ", $failed_files)
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => true, 
            'message' => 'ອັບໂຫຼດເອກະສານສຳເລັດແລ້ວ! ຂໍ້ມູນຈະຖືກກວດສອບໂດຍຜູ້ດູແລລະບົບກ່ອນສະແດງຜົນ.'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'ບໍ່ສາມາດບັນທຶກໄຟລ໌ໃສ່ຖານຂໍ້ມູນໄດ້. ' . (!empty($failed_files) ? implode("; ", $failed_files) : '')
    ], JSON_UNESCAPED_UNICODE);
}
?>

