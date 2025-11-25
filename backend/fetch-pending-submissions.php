<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດເບິ່ງການສົ່ງຂໍ້ມູນທີ່ລໍຖ້າກວດສອບ'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $submissions = [];
    
    // Fetch pending teacher colleges (is_active = false)
    $sql = "SELECT 'teacher_college' as type, college_id as id, college_name as name, title, description, file_path, NULL as link_url, date_created, created_at, created_by, is_active 
            FROM teacher_colleges 
            WHERE is_active = FALSE
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $colleges = $stmt->fetchAll();
    foreach ($colleges as $college) {
        $submissions[] = $college;
    }
    
    // Fetch pending districts (is_active = false)
    $sql = "SELECT 'district' as type, district_id as id, district_name as name, title, description, file_path, link_url, date_created, created_at, created_by, is_active 
            FROM districts 
            WHERE is_active = FALSE
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $districts = $stmt->fetchAll();
    foreach ($districts as $district) {
        $submissions[] = $district;
    }
    
    // Fetch pending provinces (is_active = false)
    $sql = "SELECT 'province' as type, province_id as id, province_name as name, title, description, file_path, link_url, date_created, created_at, created_by, is_active 
            FROM provinces 
            WHERE is_active = FALSE
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $provinces = $stmt->fetchAll();
    foreach ($provinces as $province) {
        $submissions[] = $province;
    }
    
    // Sort all submissions by created_at (newest first)
    usort($submissions, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    // Add file URLs
    $base_path = '/internal-education-worker-report/';
    foreach ($submissions as &$submission) {
        if ($submission['file_path']) {
            $file_path = ltrim($submission['file_path'], '/');
            if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                $submission['file_url'] = $base_path . ltrim($file_path, '/');
            } else {
                $submission['file_url'] = '/' . ltrim($file_path, '/');
            }
        } else {
            $submission['file_url'] = null;
        }
    }
    
    echo json_encode($submissions, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

