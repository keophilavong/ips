<?php
/**
 * Migration Script: Add link_url column to provinces table
 * Run this file in your browser to execute the migration
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migration: Add link_url to Provinces</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1e40af;
        }
        .success {
            background: #d1fae5;
            color: #059669;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info {
            background: #dbeafe;
            color: #1e40af;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Migration: Add link_url to Provinces Table</h1>
    
    <?php
    try {
        include "backend/db.php";
        
        // Check if column already exists
        $check_sql = "SELECT column_name 
                      FROM information_schema.columns 
                      WHERE table_name = 'provinces' AND column_name = 'link_url'";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute();
        $exists = $check_stmt->fetch();
        
        if ($exists) {
            echo '<div class="info">✓ Column "link_url" already exists in provinces table. No migration needed.</div>';
        } else {
            // Add the column
            $sql = "ALTER TABLE provinces ADD COLUMN link_url VARCHAR(500)";
            $conn->exec($sql);
            echo '<div class="success">✓ Successfully added "link_url" column to provinces table!</div>';
        }
        
        // Show current table structure
        $desc_sql = "SELECT column_name, data_type, character_maximum_length 
                     FROM information_schema.columns 
                     WHERE table_name = 'provinces' 
                     ORDER BY ordinal_position";
        $desc_stmt = $conn->prepare($desc_sql);
        $desc_stmt->execute();
        $columns = $desc_stmt->fetchAll();
        
        echo '<h2>Current Provinces Table Structure:</h2>';
        echo '<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #f3f4f6;"><th>Column Name</th><th>Data Type</th><th>Max Length</th></tr>';
        foreach ($columns as $col) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($col['column_name']) . '</td>';
            echo '<td>' . htmlspecialchars($col['data_type']) . '</td>';
            echo '<td>' . ($col['character_maximum_length'] ? htmlspecialchars($col['character_maximum_length']) : '-') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
    } catch(PDOException $e) {
        echo '<div class="error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>
    
    <p style="margin-top: 30px;">
        <a href="province-activities.html" style="color: #1e40af; text-decoration: none;">← Back to Province Activities</a>
    </p>
</div>
</body>
</html>

