<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດລຶບລາຍການເມນູ'], JSON_UNESCAPED_UNICODE);
    exit;
}

$menu_id = $_POST['menu_id'] ?? 0;

if (empty($menu_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID ລາຍການເມນູຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "DELETE FROM menu_items WHERE menu_id = :menu_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':menu_id' => $menu_id]);
    
    echo json_encode(['success' => true, 'message' => 'ລຶບລາຍການເມນູສຳເລັດແລ້ວ'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

