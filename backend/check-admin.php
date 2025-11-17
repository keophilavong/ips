<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['admin_id'])) {
    echo json_encode(['is_admin' => true]);
} else {
    echo json_encode(['is_admin' => false]);
}
?>

