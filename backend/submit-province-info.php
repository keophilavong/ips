<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Allow both logged-in users and guests to submit
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$province_name = $_POST['province_name'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$file_path = $_POST['file_path'] ?? null;
$link_url = $_POST['link_url'] ?? null;
$contact_name = $_POST['contact_name'] ?? '';
$contact_email = $_POST['contact_email'] ?? '';
$contact_phone = $_POST['contact_phone'] ?? '';
$date_created = $_POST['date_created'] ?? date('Y-m-d');

if (empty($province_name) || empty($title)) {
    http_response_code(400);
    echo json_encode(['error' => 'ຊື່ແຂວງ ແລະ ຫົວຂໍ້ຈຳເປັນຕ້ອງມີ'], JSON_UNESCAPED_UNICODE);
    exit;
}

// If description includes contact info, append it
if (!empty($contact_name) || !empty($contact_email) || !empty($contact_phone)) {
    $contact_info = "\n\n--- ຂໍ້ມູນຕິດຕໍ່ ---\n";
    if (!empty($contact_name)) $contact_info .= "ຊື່: " . $contact_name . "\n";
    if (!empty($contact_email)) $contact_info .= "ອີເມວ: " . $contact_email . "\n";
    if (!empty($contact_phone)) $contact_info .= "ເບີໂທ: " . $contact_phone . "\n";
    $description = ($description ? $description . "\n" : '') . $contact_info;
}

try {
    // Set is_active to false by default for user submissions (needs admin approval)
    $sql = "INSERT INTO provinces (province_name, title, description, file_path, link_url, date_created, created_by, is_active) 
            VALUES (:province_name, :title, :description, :file_path, :link_url, :date_created, :created_by, FALSE)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':province_name' => $province_name,
        ':title' => $title,
        ':description' => $description,
        ':file_path' => $file_path,
        ':link_url' => $link_url,
        ':date_created' => $date_created,
        ':created_by' => $user_id
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'ສົ່ງຂໍ້ມູນສຳເລັດແລ້ວ! ຂໍ້ມູນຈະຖືກກວດສອບໂດຍຜູ້ດູແລລະບົບກ່ອນສະແດງຜົນ.'
    ], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

