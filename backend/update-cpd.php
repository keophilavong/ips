<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດອັບເດດເນື້ອຫາ CPD'], JSON_UNESCAPED_UNICODE);
    exit;
}

$cpd_id = $_POST['cpd_id'] ?? 0;
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$content_type = $_POST['content_type'] ?? 'resource';
$file_path = $_POST['file_path'] ?? null;
$link_url = $_POST['link_url'] ?? null;
$date_created = $_POST['date_created'] ?? null;
$is_active = isset($_POST['is_active']) ? ($_POST['is_active'] === 'true' || $_POST['is_active'] === true) : true;

if (empty($title)) {
    http_response_code(400);
    echo json_encode(['error' => 'ຫົວຂໍ້ຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "UPDATE cpd_content 
            SET title = :title, description = :description, content_type = :content_type, 
                file_path = :file_path, link_url = :link_url, date_created = :date_created, is_active = :is_active,
                updated_at = CURRENT_TIMESTAMP
            WHERE cpd_id = :cpd_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':cpd_id' => $cpd_id,
        ':title' => $title,
        ':description' => $description,
        ':content_type' => $content_type,
        ':file_path' => $file_path,
        ':link_url' => $link_url,
        ':date_created' => $date_created,
        ':is_active' => $is_active
    ]);
    
    echo json_encode(['success' => true, 'message' => 'ອັບເດດເນື້ອຫາ CPD ສຳເລັດແລ້ວ'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

