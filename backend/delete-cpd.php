<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດລຶບເນື້ອຫາ CPD'], JSON_UNESCAPED_UNICODE);
    exit;
}

$cpd_id = $_POST['cpd_id'] ?? 0;

if (empty($cpd_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID ເນື້ອຫາ CPD ຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "SELECT file_path FROM cpd_content WHERE cpd_id = :cpd_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cpd_id' => $cpd_id]);
    $row = $stmt->fetch();
    
    if ($row && $row['file_path']) {
        $file_path = '../' . ltrim($row['file_path'], '/');
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $sql = "DELETE FROM cpd_content WHERE cpd_id = :cpd_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cpd_id' => $cpd_id]);
    
    echo json_encode(['success' => true, 'message' => 'ລຶບເນື້ອຫາ CPD ສຳເລັດແລ້ວ'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

