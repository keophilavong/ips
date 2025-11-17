<?php
session_start();
include "db.php";

$username = $_POST['username'];
$password = $_POST['password'];

try {
    $sql = "SELECT * FROM admins WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        echo "success";
    } else {
        echo "error";
    }
} catch(PDOException $e) {
    echo "error";
}
?>

