<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດເພີ່ມເນື້ອຫາ CPD'], JSON_UNESCAPED_UNICODE);
    exit;
}

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$content_type = $_POST['content_type'] ?? 'resource';
$file_path = $_POST['file_path'] ?? null;
$link_url = $_POST['link_url'] ?? null;
$date_created = $_POST['date_created'] ?? date('Y-m-d');

if (empty($title)) {
    http_response_code(400);
    echo json_encode(['error' => 'ຫົວຂໍ້ຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "INSERT INTO cpd_content (title, description, content_type, file_path, link_url, date_created, created_by) 
            VALUES (:title, :description, :content_type, :file_path, :link_url, :date_created, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':content_type' => $content_type,
        ':file_path' => $file_path,
        ':link_url' => $link_url,
        ':date_created' => $date_created
    ]);
    
    echo json_encode(['success' => true, 'message' => 'ເພີ່ມເນື້ອຫາ CPD ສຳເລັດແລ້ວ'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

