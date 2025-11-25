<?php
/**
 * Database Tables Setup Script
 * Run this file once to create all content management tables
 * Access via: http://localhost/internal-education-worker-report/setup-tables.php
 */

// Include database connection
include "backend/db.php";

// Read the SQL file
$sqlFile = __DIR__ . '/database_content_tables.sql';
$sql = file_get_contents($sqlFile);

if (!$sql) {
    die("Error: Could not read database_content_tables.sql file");
}

// Split SQL into individual statements
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    function($stmt) {
        return !empty($stmt) && 
               !preg_match('/^--/', $stmt) && 
               !preg_match('/^\/\*/', $stmt);
    }
);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: #059669; background: #d1fae5; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #1e40af; background: #dbeafe; padding: 10px; border-radius: 5px; margin: 10px 0; }
        h1 { color: #1e40af; }
    </style>
</head>
<body>
    <h1>Database Tables Setup</h1>";

try {
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $conn->exec($statement);
            $successCount++;
            echo "<div class='success'>✓ Executed successfully</div>";
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            
            // Ignore "already exists" errors
            if (strpos($errorMsg, 'already exists') !== false || 
                strpos($errorMsg, 'duplicate') !== false) {
                echo "<div class='info'>ℹ Already exists (skipped)</div>";
            }
            // Ignore permission errors for indexes (tables still work without them)
            else if (strpos($errorMsg, 'Insufficient privilege') !== false || 
                     strpos($errorMsg, 'must be owner') !== false) {
                echo "<div class='info'>ℹ Index creation skipped (permission issue - not critical)</div>";
            }
            // Show other errors
            else {
                $errorCount++;
                echo "<div class='error'>✗ Error: " . htmlspecialchars($errorMsg) . "</div>";
            }
        }
    }
    
    echo "<div class='info'><strong>Setup Complete!</strong><br>";
    echo "Successfully executed: $successCount statements<br>";
    if ($errorCount > 0) {
        echo "Critical errors: $errorCount statements<br>";
        echo "<small style='color: #64748b;'>Note: Permission errors for indexes are not critical - tables will work fine without them.</small><br>";
    }
    echo "</div>";
    
    // Verify tables were created
    echo "<h2>Verification</h2>";
    $tables = ['teacher_colleges', 'internal_workers', 'districts', 'provinces', 'cpd_content'];
    $existingTables = [];
    
    foreach ($tables as $table) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM $table");
            $existingTables[] = $table;
            echo "<div class='success'>✓ Table '$table' exists</div>";
        } catch (PDOException $e) {
            echo "<div class='error'>✗ Table '$table' does not exist</div>";
        }
    }
    
    if (count($existingTables) === count($tables)) {
        echo "<div class='success'><strong>All tables created successfully! You can now use the admin panel.</strong></div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>Fatal Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";
?>

