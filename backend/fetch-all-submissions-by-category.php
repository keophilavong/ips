<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດເບິ່ງຂໍ້ມູນນີ້'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Function to get the base path dynamically
function getBasePath() {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $request_uri = $_SERVER['REQUEST_URI'];
    $base_path = str_replace(basename($script_name), '', $script_name);
    // If the request URI contains the base path, use it
    if (strpos($request_uri, $base_path) === 0) {
        return $base_path;
    }
    // Fallback for different server configurations (e.g., XAMPP in htdocs/ips)
    // This attempts to find the segment before 'backend/'
    $parts = explode('/backend/', $request_uri);
    if (count($parts) > 1) {
        return $parts[0] . '/';
    }
    return '/'; // Default to root
}

// Helper function to get correct file URL
function getFileUrl($file_path) {
    if (!$file_path) return null;
    
    // If already a full URL, return as is
    if (strpos($file_path, 'http') === 0) {
        return $file_path;
    }
    
    // Remove any ../ prefixes
    $cleanPath = str_replace('../', '', $file_path);
    $cleanPath = ltrim($cleanPath, '/');
    
    // Prepend base path
    return getBasePath() . $cleanPath;
}

try {
    $result = [
        'teacher_college' => [],
        'internal_worker' => [],
        'district' => [],
        'province' => []
    ];
    
    // 1. Teacher Colleges (ຂໍ້ມູນການເຄື່ອນໄຫວຂອງວິທະຍາໄລຄູ)
    $sql = "SELECT college_id as id, college_name as name, title, description, file_path, NULL as link_url, date_created, created_at, created_by, is_active 
            FROM teacher_colleges 
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $colleges = $stmt->fetchAll();
    foreach ($colleges as &$college) {
        $college['file_url'] = getFileUrl($college['file_path']);
    }
    $result['teacher_college'] = $colleges;
    
    // 2. Internal Workers (ຜູ້ເຮັດວຽກສຶກສານິເທດພາຍໃນ)
    $sql = "SELECT worker_id as id, worker_name as name, title, description, file_path, NULL as link_url, date_created, created_at, created_by, is_active 
            FROM internal_workers 
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $workers = $stmt->fetchAll();
    foreach ($workers as &$worker) {
        $worker['file_url'] = getFileUrl($worker['file_path']);
    }
    $result['internal_worker'] = $workers;
    
    // 3. Districts (ຫ້ອງການສຶກສາທິການ ແລະ ກິລາເມືອງ)
    $sql = "SELECT district_id as id, district_name as name, title, description, file_path, link_url, date_created, created_at, created_by, is_active 
            FROM districts 
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $districts = $stmt->fetchAll();
    foreach ($districts as &$district) {
        $district['file_url'] = getFileUrl($district['file_path']);
    }
    $result['district'] = $districts;
    
    // 4. Provinces (ພະແນກສຶກສາທິການ ແລະ ກິລາແຂວງ)
    $sql = "SELECT province_id as id, province_name as name, title, description, file_path, link_url, date_created, created_at, created_by, is_active 
            FROM provinces 
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $provinces = $stmt->fetchAll();
    foreach ($provinces as &$province) {
        $province['file_url'] = getFileUrl($province['file_path']);
    }
    $result['province'] = $provinces;
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

