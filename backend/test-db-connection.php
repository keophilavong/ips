<?php
// Test Database Connection Script
// This file helps diagnose database connection issues
// Access via: http://localhost:8080/backend/test-db-connection.php

header('Content-Type: application/json; charset=utf-8');

// Include database configuration
include "db.php";

$result = [
    'environment_variables' => [
        'DB_HOST' => getenv('DB_HOST') ?: (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : (isset($_SERVER['DB_HOST']) ? $_SERVER['DB_HOST'] : 'NOT SET')),
        'DB_PORT' => getenv('DB_PORT') ?: (isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : (isset($_SERVER['DB_PORT']) ? $_SERVER['DB_PORT'] : 'NOT SET')),
        'DB_USER' => getenv('DB_USER') ?: (isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : (isset($_SERVER['DB_USER']) ? $_SERVER['DB_USER'] : 'NOT SET')),
        'DB_PASS' => getenv('DB_PASS') ?: (isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : (isset($_SERVER['DB_PASS']) ? $_SERVER['DB_PASS'] : 'NOT SET')) ? '***SET***' : 'NOT SET',
        'DB_NAME' => getenv('DB_NAME') ?: (isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : (isset($_SERVER['DB_NAME']) ? $_SERVER['DB_NAME'] : 'NOT SET')),
    ],
    'connection_status' => 'unknown',
    'connection_error' => null,
    'pdo_available' => extension_loaded('pdo'),
    'pdo_pgsql_available' => extension_loaded('pdo_pgsql'),
    'php_version' => phpversion(),
];

// Check if connection was successful
if ($conn !== null) {
    $result['connection_status'] = 'success';
    
    // Try a simple query
    try {
        $stmt = $conn->query("SELECT version() as version");
        $version = $stmt->fetch();
        $result['database_version'] = $version['version'] ?? 'Unknown';
        
        // Check if tables exist
        $stmt = $conn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $result['tables_found'] = $tables;
        $result['table_count'] = count($tables);
        
        // Check for admins table specifically
        $result['admins_table_exists'] = in_array('admins', $tables);
        $result['users_table_exists'] = in_array('users', $tables);
        
    } catch (PDOException $e) {
        $result['connection_status'] = 'connected_but_query_failed';
        $result['query_error'] = $e->getMessage();
    }
} else {
    $result['connection_status'] = 'failed';
    $result['connection_error'] = isset($db_error) ? $db_error : 'Connection object is null';
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

