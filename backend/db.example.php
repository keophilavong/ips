<?php
// Database Configuration Template
// Copy this file to db.php and update with your production database credentials

// Production Database Configuration
$host = "your-database-host";        // e.g., "localhost" or "db.example.com"
$port = "5432";                       // Default PostgreSQL port
$user = "your-database-username";     // Your database username
$pass = "your-secure-password";       // Your database password
$dbname = "your-database-name";      // Your database name

try {
    // Create PDO connection for PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $conn = new PDO($dsn, $user, $pass);
    
    // Set error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

