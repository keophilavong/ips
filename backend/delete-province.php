<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດລຶບຂໍ້ມູນແຂວງ'], JSON_UNESCAPED_UNICODE);
    exit;
}

$province_id = $_POST['province_id'] ?? 0;

if (empty($province_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID ຂໍ້ມູນແຂວງຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "SELECT file_path FROM provinces WHERE province_id = :province_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':province_id' => $province_id]);
    $row = $stmt->fetch();
    
    if ($row && $row['file_path']) {
        $file_path = '../' . ltrim($row['file_path'], '/');
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $sql = "DELETE FROM provinces WHERE province_id = :province_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':province_id' => $province_id]);
    
    echo json_encode(['success' => true, 'message' => 'ລຶບຂໍ້ມູນແຂວງສຳເລັດແລ້ວ'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

