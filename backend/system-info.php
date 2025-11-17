<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$info = [
    'php_version' => PHP_VERSION,
    'server_time' => date('Y-m-d H:i:s'),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
];

echo json_encode($info, JSON_UNESCAPED_UNICODE);
?>

