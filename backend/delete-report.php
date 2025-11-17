<?php
include "db.php";

$report_id = $_POST['report_id'];

try {
    // Get file path before deleting
    $sql = "SELECT file_path FROM reports WHERE report_id = :report_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':report_id' => $report_id]);
    $row = $stmt->fetch();
    
    if ($row) {
        $file_path = $row['file_path'];
        
        // Delete file if exists
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete from database
    $sql = "DELETE FROM reports WHERE report_id = :report_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':report_id' => $report_id]);
    
    echo "success";
} catch(PDOException $e) {
    echo "error: " . $e->getMessage();
}
?>

