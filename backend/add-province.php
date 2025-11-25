<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດເພີ່ມຂໍ້ມູນແຂວງ'], JSON_UNESCAPED_UNICODE);
    exit;
}

$province_name = $_POST['province_name'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$file_path = $_POST['file_path'] ?? null;
$link_url = $_POST['link_url'] ?? null;
$date_created = $_POST['date_created'] ?? date('Y-m-d');

if (empty($province_name) || empty($title)) {
    http_response_code(400);
    echo json_encode(['error' => 'ຊື່ແຂວງ ແລະ ຫົວຂໍ້ຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "INSERT INTO provinces (province_name, title, description, file_path, link_url, date_created, created_by) 
            VALUES (:province_name, :title, :description, :file_path, :link_url, :date_created, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':province_name' => $province_name,
        ':title' => $title,
        ':description' => $description,
        ':file_path' => $file_path,
        ':link_url' => $link_url,
        ':date_created' => $date_created
    ]);
    
    echo json_encode(['success' => true, 'message' => 'ເພີ່ມຂໍ້ມູນແຂວງສຳເລັດແລ້ວ'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

