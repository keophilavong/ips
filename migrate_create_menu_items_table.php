<?php
// migrate_create_menu_items_table.php
session_start();
include "db.php"; // Your database connection file

header('Content-Type: text/html; charset=utf-8');

// Check if the user is an admin (optional, but good practice for migrations)
if (!isset($_SESSION['admin_id'])) {
    echo "<h2>Access Denied</h2>";
    echo "<p>You must be logged in as an administrator to run this migration.</p>";
    exit;
}

echo "<h2>Running Migration: Create menu_items table</h2>";

try {
    // Read and execute the SQL migration
    $sql = file_get_contents('migration_create_menu_items_table.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^\s*--/', $statement)) {
            $stmt = $conn->prepare($statement);
            $stmt->execute();
        }
    }
    
    echo "<p style='color: green;'>Migration successful: 'menu_items' table created (if it didn't exist).</p>";
    
    // Now populate with initial data
    echo "<h3>Populating initial menu items...</h3>";
    $populateSql = file_get_contents('migration_populate_menu_items.sql');
    $populateStatements = array_filter(array_map('trim', explode(';', $populateSql)));
    
    foreach ($populateStatements as $statement) {
        if (!empty($statement) && !preg_match('/^\s*--/', $statement)) {
            $stmt = $conn->prepare($statement);
            $stmt->execute();
        }
    }
    
    echo "<p style='color: green;'>Initial menu items populated successfully.</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Migration failed: " . $e->getMessage() . "</p>";
}

echo "<p><a href='../admin/manage-menu-items.html'>Go to Manage Menu Items</a></p>";
echo "<p><a href='../admin/dashboard.html'>Go to Admin Dashboard</a></p>";
?>

