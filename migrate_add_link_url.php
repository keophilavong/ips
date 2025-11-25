<?php
/**
 * Migration: Add link_url column to districts table
 * Run this file once to add the link_url column
 * Access via: http://localhost/internal-education-worker-report/migrate_add_link_url.php
 */

// Include database connection
include "backend/db.php";

echo "<!DOCTYPE html>
<html>
<head>
    <title>Add link_url Column Migration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: #059669; background: #d1fae5; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc2626; background: #fee2e2; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: #1e40af; background: #dbeafe; padding: 15px; border-radius: 5px; margin: 10px 0; }
        h1 { color: #1e40af; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Add link_url Column to Districts Table</h1>";

try {
    // Check if column already exists
    $checkSql = "SELECT column_name 
                 FROM information_schema.columns 
                 WHERE table_name = 'districts' 
                 AND column_name = 'link_url'";
    
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute();
    $columnExists = $checkStmt->fetch();
    
    if ($columnExists) {
        echo "<div class='info'>
            <strong>✓ Column already exists</strong><br>
            The <code>link_url</code> column already exists in the <code>districts</code> table. No migration needed.
        </div>";
    } else {
        // Add the column
        $migrationSql = "ALTER TABLE districts ADD COLUMN link_url VARCHAR(500)";
        $conn->exec($migrationSql);
        
        echo "<div class='success'>
            <strong>✓ Migration successful!</strong><br>
            The <code>link_url</code> column has been added to the <code>districts</code> table.
        </div>";
    }
    
    // Verify the column exists
    $verifyStmt = $conn->prepare($checkSql);
    $verifyStmt->execute();
    $verified = $verifyStmt->fetch();
    
    if ($verified) {
        echo "<div class='info'>
            <strong>Verification:</strong> The <code>link_url</code> column is now present in the districts table.
        </div>";
    }
    
} catch(PDOException $e) {
    echo "<div class='error'>
        <strong>✗ Migration failed</strong><br>
        Error: " . htmlspecialchars($e->getMessage()) . "<br><br>
        <strong>Manual fix:</strong> Run this SQL command in pgAdmin or your PostgreSQL client:<br>
        <code>ALTER TABLE districts ADD COLUMN link_url VARCHAR(500);</code>
    </div>";
}

echo "<div class='info' style='margin-top: 30px;'>
    <strong>Next steps:</strong><br>
    1. Refresh your internal worker page<br>
    2. The districts should now load correctly with the link_url field
</div>";

echo "</body></html>";
?>

