<?php
header('Content-Type: application/json; charset=utf-8');
include "db.php";

// Check if database connection failed
if ($conn === null) {
    $errorMsg = isset($db_error) ? $db_error : 'Database connection failed';
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed. Please check your database configuration.',
        'error' => $errorMsg
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($fullname) || empty($email) || empty($password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (fullname, email, password) VALUES (:fullname, :email, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':fullname' => $fullname,
        ':email' => $email,
        ':password' => $hashedPassword
    ]);
    echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    // Check if it's a duplicate email error
    if (strpos($e->getMessage(), 'duplicate') !== false || strpos($e->getMessage(), 'unique') !== false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email already exists. Please use a different email.'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Registration failed: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>

