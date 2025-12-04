<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດອະນຸມັດການສົ່ງຂໍ້ມູນ'], JSON_UNESCAPED_UNICODE);
    exit;
}

$type = $_POST['type'] ?? ''; // 'teacher_college', 'internal_worker', 'district', or 'province'
$id = $_POST['id'] ?? 0;
$action = $_POST['action'] ?? 'approve'; // 'approve' or 'reject'

if (empty($type) || empty($id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ປະເພດ ແລະ ID ຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $table_name = '';
    $id_column = '';
    
    switch ($type) {
        case 'teacher_college':
            $table_name = 'teacher_colleges';
            $id_column = 'college_id';
            break;
        case 'internal_worker':
            $table_name = 'internal_workers';
            $id_column = 'worker_id';
            break;
        case 'district':
            $table_name = 'districts';
            $id_column = 'district_id';
            break;
        case 'province':
            $table_name = 'provinces';
            $id_column = 'province_id';
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'ປະເພດບໍ່ຖືກຕ້ອງ'], JSON_UNESCAPED_UNICODE);
            exit;
    }
    
    if ($action === 'approve') {
        $sql = "UPDATE {$table_name} SET is_active = TRUE, updated_at = CURRENT_TIMESTAMP WHERE {$id_column} = :id";
        $message = 'ອະນຸມັດການສົ່ງຂໍ້ມູນສຳເລັດແລ້ວ';
    } else {
        // Reject - delete the submission
        $sql = "DELETE FROM {$table_name} WHERE {$id_column} = :id";
        $message = 'ປະຕິເສດການສົ່ງຂໍ້ມູນສຳເລັດແລ້ວ';
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    echo json_encode(['success' => true, 'message' => $message], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

