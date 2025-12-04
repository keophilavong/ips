<?php
// Diagnostic Page - Check system configuration
// Access via: http://localhost:8080/backend/diagnose.php

header('Content-Type: application/json; charset=utf-8');

$diagnosis = [
    'php_version' => phpversion(),
    'pdo_available' => extension_loaded('pdo'),
    'pdo_pgsql_available' => extension_loaded('pdo_pgsql'),
    'environment_variables' => [],
    'docker_check' => [],
    'file_permissions' => [],
    'database_connection' => []
];

// Check environment variables
$envVars = ['DB_HOST', 'DB_PORT', 'DB_USER', 'DB_PASS', 'DB_NAME'];
foreach ($envVars as $var) {
    $value = getenv($var);
    if ($value === false) {
        $value = isset($_ENV[$var]) ? $_ENV[$var] : (isset($_SERVER[$var]) ? $_SERVER[$var] : null);
    }
    $diagnosis['environment_variables'][$var] = $value !== false && $value !== null ? ($var === 'DB_PASS' ? '***SET***' : $value) : 'NOT SET';
}

// Check if running in Docker
$diagnosis['docker_check']['is_docker'] = file_exists('/.dockerenv') || getenv('container') !== false;
$diagnosis['docker_check']['container_name'] = getenv('HOSTNAME') ?: 'Unknown';

// Check file permissions
$diagnosis['file_permissions']['db_php_exists'] = file_exists(__DIR__ . '/db.php');
$diagnosis['file_permissions']['db_php_readable'] = is_readable(__DIR__ . '/db.php');

// Try database connection
include "db.php";

if ($conn !== null) {
    $diagnosis['database_connection']['status'] = 'success';
    try {
        $stmt = $conn->query("SELECT version() as version");
        $version = $stmt->fetch();
        $diagnosis['database_connection']['version'] = $version['version'] ?? 'Unknown';
    } catch (PDOException $e) {
        $diagnosis['database_connection']['query_error'] = $e->getMessage();
    }
} else {
    $diagnosis['database_connection']['status'] = 'failed';
    $diagnosis['database_connection']['error'] = isset($db_error) ? $db_error : 'Connection object is null';
    
    // Show what values were used for connection attempt
    $diagnosis['database_connection']['attempted_connection'] = [
        'host' => getenv('DB_HOST') ?: (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : (isset($_SERVER['DB_HOST']) ? $_SERVER['DB_HOST'] : 'localhost')),
        'port' => getenv('DB_PORT') ?: (isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : (isset($_SERVER['DB_PORT']) ? $_SERVER['DB_PORT'] : '5432')),
        'user' => getenv('DB_USER') ?: (isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : (isset($_SERVER['DB_USER']) ? $_SERVER['DB_USER'] : 'postgres')),
        'dbname' => getenv('DB_NAME') ?: (isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : (isset($_SERVER['DB_NAME']) ? $_SERVER['DB_NAME'] : 'edu-pro')),
    ];
}

// Recommendations
$recommendations = [];
if (!$diagnosis['pdo_pgsql_available']) {
    $recommendations[] = 'PDO PostgreSQL extension is not installed. Install php-pgsql package.';
}
if ($diagnosis['environment_variables']['DB_HOST'] === 'NOT SET') {
    $recommendations[] = 'DB_HOST environment variable is not set. If using Docker, check docker-compose.yml. If using XAMPP, edit backend/db.php directly.';
}
if ($diagnosis['database_connection']['status'] === 'failed') {
    $recommendations[] = 'Database connection failed. Check: 1) Database server is running, 2) Network connectivity to ' . ($diagnosis['database_connection']['attempted_connection']['host'] ?? 'database server'), 3) Credentials are correct, 4) Firewall allows connection.';
}
if (!$diagnosis['docker_check']['is_docker'] && $diagnosis['environment_variables']['DB_HOST'] === 'NOT SET') {
    $recommendations[] = 'You appear to be running XAMPP (not Docker). Please edit backend/db.php directly with your database credentials instead of using environment variables.';
}

$diagnosis['recommendations'] = $recommendations;

echo json_encode($diagnosis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

