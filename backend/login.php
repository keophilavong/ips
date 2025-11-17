<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Accept either email or username
$identifier = $_POST['identifier'] ?? $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($identifier) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing credentials'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // First, try to login as admin (check by username)
    $sql = "SELECT * FROM admins WHERE username = :identifier";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':identifier' => $identifier]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        // Admin found, verify password
        // Try both with and without trimming (in case of whitespace issues)
        $verifyResult = password_verify($password, $admin['password']);
        $verifyTrimmed = password_verify(trim($password), trim($admin['password']));
        
        if ($verifyResult || $verifyTrimmed) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            echo json_encode(['status' => 'success', 'type' => 'admin'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Admin found but password wrong - don't continue to user check
        // Return more detailed error for debugging
        echo json_encode([
            'status' => 'error', 
            'message' => 'Invalid password',
            'debug_info' => 'Admin found but password verification failed. Run FORCE_FIX_ADMIN.php to reset password.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // If not admin, try as regular user (check by email)
    $sql = "SELECT * FROM users WHERE email = :identifier";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':identifier' => $identifier]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'] ?? 'user';
        echo json_encode(['status' => 'success', 'type' => 'user'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // If neither worked, return error
    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials'], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
