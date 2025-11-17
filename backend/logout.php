<?php
session_start();

// Destroy all session data
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['success' => true, 'message' => 'Logged out successfully'], JSON_UNESCAPED_UNICODE);
?>
