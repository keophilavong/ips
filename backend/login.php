<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Include database connection
include "db.php";

// Check if database connection failed
if ($conn === null) {
    $errorMsg = isset($db_error) ? $db_error : 'Database connection failed';
    
    // Get environment variables for debugging
    $envInfo = [
        'DB_HOST' => getenv('DB_HOST') ?: (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : (isset($_SERVER['DB_HOST']) ? $_SERVER['DB_HOST'] : 'NOT SET')),
        'DB_PORT' => getenv('DB_PORT') ?: (isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : (isset($_SERVER['DB_PORT']) ? $_SERVER['DB_PORT'] : 'NOT SET')),
        'DB_USER' => getenv('DB_USER') ?: (isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : (isset($_SERVER['DB_USER']) ? $_SERVER['DB_USER'] : 'NOT SET')),
        'DB_NAME' => getenv('DB_NAME') ?: (isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : (isset($_SERVER['DB_NAME']) ? $_SERVER['DB_NAME'] : 'NOT SET')),
    ];
    
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database connection failed: ' . $errorMsg,
        'error' => $errorMsg,
        'debug_info' => $envInfo,
        'suggestion' => 'Please check: 1) Database server is running, 2) Credentials are correct, 3) Network connectivity, 4) Firewall settings'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

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
