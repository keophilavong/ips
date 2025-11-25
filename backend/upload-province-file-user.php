<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Allow both logged-in users and guests to upload files
$allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
$max_file_size = 100 * 1024 * 1024; // 100MB

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'ບໍ່ມີໄຟລ໌ຖືກອັບໂຫຼດ ຫຼື ເກີດຂໍ້ຜິດພາດໃນການອັບໂຫຼດ'], JSON_UNESCAPED_UNICODE);
    exit;
}

$file = $_FILES['file'];
$filename = basename($file['name']);
$file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$file_size = $file['size'];

if (!in_array($file_extension, $allowed_extensions)) {
    http_response_code(400);
    echo json_encode(['error' => 'ປະເພດໄຟລ໌ "' . strtoupper($file_extension) . '" ບໍ່ຖືກອະນຸຍາດ'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($file_size > $max_file_size) {
    http_response_code(400);
    echo json_encode(['error' => 'ຂະໜາດໄຟລ໌ເກີນຂະໜາດສູງສຸດທີ່ອະນຸຍາດ 100MB'], JSON_UNESCAPED_UNICODE);
    exit;
}

$target_dir = "../files/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$unique_filename = time() . "_" . uniqid() . "_" . $filename;
$target_file = $target_dir . $unique_filename;

if (move_uploaded_file($file['tmp_name'], $target_file)) {
    $relative_path = 'files/' . $unique_filename;
    echo json_encode([
        'success' => true,
        'file_path' => $relative_path,
        'file_url' => '/' . $relative_path,
        'message' => 'ອັບໂຫຼດໄຟລ໌ສຳເລັດແລ້ວ'
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'ບໍ່ສາມາດອັບໂຫຼດໄຟລ໌ໄດ້. ກະລຸນາກວດສອບສິດອະນຸຍາດໄຟລ໌.'], JSON_UNESCAPED_UNICODE);
}
?>

