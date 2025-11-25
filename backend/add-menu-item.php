<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດເພີ່ມລາຍການເມນູ'], JSON_UNESCAPED_UNICODE);
    exit;
}

$menu_text = $_POST['menu_text'] ?? '';
$menu_icon = $_POST['menu_icon'] ?? '';
$menu_url = $_POST['menu_url'] ?? '';
$menu_row = $_POST['menu_row'] ?? 'top';
$display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
$is_active = isset($_POST['is_active']) ? ($_POST['is_active'] === 'true' || $_POST['is_active'] === true) : true;

if (empty($menu_text) || empty($menu_url)) {
    http_response_code(400);
    echo json_encode(['error' => 'ຂໍ້ຄວາມເມນູ ແລະ URL ຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!in_array($menu_row, ['top', 'bottom'])) {
    $menu_row = 'top';
}

try {
    $sql = "INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active, created_by) 
            VALUES (:menu_text, :menu_icon, :menu_url, :menu_row, :display_order, :is_active, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':menu_text' => $menu_text,
        ':menu_icon' => $menu_icon,
        ':menu_url' => $menu_url,
        ':menu_row' => $menu_row,
        ':display_order' => $display_order,
        ':is_active' => $is_active
    ]);
    
    echo json_encode(['success' => true, 'message' => 'ເພີ່ມລາຍການເມນູສຳເລັດແລ້ວ'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

