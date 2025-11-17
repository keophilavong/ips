<?php
include "db.php";

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO users (fullname, email, password) VALUES (:fullname, :email, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':fullname' => $fullname,
        ':email' => $email,
        ':password' => $password
    ]);
    echo "success";
} catch(PDOException $e) {
    echo "error: " . $e->getMessage();
}
?>

