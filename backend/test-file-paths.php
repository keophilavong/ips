<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Test file path detection
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$base_path = rtrim($script_dir, '/backend');
$base_path = rtrim($base_path, '/');

// Test with a sample file path
$test_file_path = 'uploads/activities/test.jpg';
$cleanPath = str_replace('../', '', $test_file_path);
$cleanPath = ltrim($cleanPath, '/');

if ($base_path && $base_path !== '/') {
    $generated_url = $base_path . '/' . $cleanPath;
} else {
    $generated_url = '/' . $cleanPath;
}

$result = [
    'script_name' => $_SERVER['SCRIPT_NAME'],
    'script_dir' => $script_dir,
    'base_path' => $base_path,
    'test_file_path' => $test_file_path,
    'generated_url' => $generated_url,
    'server_document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Not set',
    'server_request_uri' => $_SERVER['REQUEST_URI'] ?? 'Not set',
    'actual_file_exists' => file_exists('../uploads/activities/') ? 'Directory exists' : 'Directory does not exist',
    'files_dir_exists' => file_exists('../files/') ? 'Directory exists' : 'Directory does not exist',
];

// Check if we can list files
if (file_exists('../files/')) {
    $files = scandir('../files/');
    $result['files_in_files_dir'] = array_slice($files, 2, 5); // First 5 files (skip . and ..)
}

if (file_exists('../uploads/activities/')) {
    $files = scandir('../uploads/activities/');
    $result['files_in_uploads_activities'] = array_slice($files, 2, 5); // First 5 files
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

