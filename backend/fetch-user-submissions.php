<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'ມີແຕ່ຜູ້ດູແລລະບົບເທົ່ານັ້ນທີ່ສາມາດເບິ່ງຂໍ້ມູນນີ້'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $category = isset($_GET['category']) ? trim($_GET['category']) : null;
    $submissions = [];
    $base_path = '/internal-education-worker-report/';
    
    // 1. Teacher College submissions (from reports table with category = college name)
    // Note: upload-handler.php sets user_id to null for admin uploads, so we check for user_id IS NOT NULL
    if (!$category || $category === 'teacher_college') {
        // Get from reports table (user submissions from teacher-college.html)
        $sql = "SELECT 'teacher_college' as submission_type, 
                       'ຂໍ້ມູນການເຄື່ອນໄຫວຂອງວິທະຍາໄລຄູ' as category_name,
                       report_id as id, 
                       category as name, 
                       title, 
                       description, 
                       file_path, 
                       NULL as link_url,
                       created_at, 
                       user_id as created_by,
                       NULL as date_created
                FROM reports 
                WHERE user_id IS NOT NULL 
                AND category IN ('ຫຼວງນໍ້າທາ', 'ຫຼວງພະບາງ', 'ປານເກີນ', 'ຄົງຄໍາຊາງ', 'ຄັງໄຂ', 'ອະຫວັນນະເຂດ', 'ປາກເຊ', 'ອາລະວັນ', 'ບ້ານເກີນ', 'ດົງຄໍາຊາງ', 'ສະຫວັນນະເຂດ', 'ສາລະວັນ')
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $teacher_colleges = $stmt->fetchAll();
        
        foreach ($teacher_colleges as $tc) {
            if ($tc['file_path']) {
                $file_path = ltrim($tc['file_path'], '/');
                if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                    $tc['file_url'] = $base_path . ltrim($file_path, '/');
                } else {
                    $tc['file_url'] = '/' . ltrim($file_path, '/');
                }
            } else {
                $tc['file_url'] = null;
            }
            $submissions[] = $tc;
        }
        
        // Also get from teacher_colleges table (user submissions)
        $sql = "SELECT 'teacher_college' as submission_type,
                       'ຂໍ້ມູນການເຄື່ອນໄຫວຂອງວິທະຍາໄລຄູ' as category_name,
                       college_id as id,
                       college_name as name,
                       title,
                       description,
                       file_path,
                       NULL as link_url,
                       created_at,
                       created_by,
                       date_created
                FROM teacher_colleges
                WHERE created_by IS NOT NULL
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $teacher_colleges_table = $stmt->fetchAll();
        
        foreach ($teacher_colleges_table as $tc) {
            if ($tc['file_path']) {
                $file_path = ltrim($tc['file_path'], '/');
                if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                    $tc['file_url'] = $base_path . ltrim($file_path, '/');
                } else {
                    $tc['file_url'] = '/' . ltrim($file_path, '/');
                }
            } else {
                $tc['file_url'] = null;
            }
            $submissions[] = $tc;
        }
    }
    
    // 2. Internal Workers submissions
    if (!$category || $category === 'internal_worker') {
        $sql = "SELECT 'internal_worker' as submission_type,
                       'ຜູ້ເຮັດວຽກສຶກສານິເທດພາຍໃນ' as category_name,
                       worker_id as id,
                       worker_name as name,
                       title,
                       description,
                       file_path,
                       NULL as link_url,
                       created_at,
                       created_by,
                       date_created
                FROM internal_workers
                WHERE created_by IS NOT NULL
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $workers = $stmt->fetchAll();
        
        foreach ($workers as $worker) {
            if ($worker['file_path']) {
                $file_path = ltrim($worker['file_path'], '/');
                if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                    $worker['file_url'] = $base_path . ltrim($file_path, '/');
                } else {
                    $worker['file_url'] = '/' . ltrim($file_path, '/');
                }
            } else {
                $worker['file_url'] = null;
            }
            $submissions[] = $worker;
        }
    }
    
    // 3. District submissions
    if (!$category || $category === 'district') {
        $sql = "SELECT 'district' as submission_type,
                       'ຫ້ອງການສຶກສາທິການແລະກິລາເມືອງ' as category_name,
                       district_id as id,
                       district_name as name,
                       title,
                       description,
                       file_path,
                       link_url,
                       created_at,
                       created_by,
                       date_created
                FROM districts
                WHERE created_by IS NOT NULL
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $districts = $stmt->fetchAll();
        
        foreach ($districts as $district) {
            if ($district['file_path']) {
                $file_path = ltrim($district['file_path'], '/');
                if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                    $district['file_url'] = $base_path . ltrim($file_path, '/');
                } else {
                    $district['file_url'] = '/' . ltrim($file_path, '/');
                }
            } else {
                $district['file_url'] = null;
            }
            $submissions[] = $district;
        }
    }
    
    // 4. Province submissions
    if (!$category || $category === 'province') {
        $sql = "SELECT 'province' as submission_type,
                       'ພະແນກສຶກສາທິການແລະກິລາແຂວງ' as category_name,
                       province_id as id,
                       province_name as name,
                       title,
                       description,
                       file_path,
                       link_url,
                       created_at,
                       created_by,
                       date_created
                FROM provinces
                WHERE created_by IS NOT NULL
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $provinces = $stmt->fetchAll();
        
        foreach ($provinces as $province) {
            if ($province['file_path']) {
                $file_path = ltrim($province['file_path'], '/');
                if (strpos($file_path, 'internal-education-worker-report') !== 0) {
                    $province['file_url'] = $base_path . ltrim($file_path, '/');
                } else {
                    $province['file_url'] = '/' . ltrim($file_path, '/');
                }
            } else {
                $province['file_url'] = null;
            }
            $submissions[] = $province;
        }
    }
    
    // Sort all submissions by created_at (newest first)
    usort($submissions, function($a, $b) {
        $timeA = strtotime($a['created_at'] ?? $a['date_created'] ?? '1970-01-01');
        $timeB = strtotime($b['created_at'] ?? $b['date_created'] ?? '1970-01-01');
        return $timeB - $timeA;
    });
    
    // Group by category
    $grouped = [];
    foreach ($submissions as $submission) {
        $cat = $submission['category_name'];
        if (!isset($grouped[$cat])) {
            $grouped[$cat] = [];
        }
        $grouped[$cat][] = $submission;
    }
    
    echo json_encode([
        'submissions' => $submissions,
        'grouped' => $grouped,
        'total' => count($submissions)
    ], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

