<?php
/**
 * Migration: Populate initial menu items
 * Run this file once to populate default menu items
 * Access via: http://localhost/internal-education-worker-report/migrate_populate_menu_items.php
 */

// Include database connection
include "backend/db.php";

echo "<!DOCTYPE html>
<html>
<head>
    <title>Populate Menu Items Migration</title>
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
    <h1>Populate Initial Menu Items</h1>";

try {
    // Check if menu_items table exists
    $checkTable = "SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_name = 'menu_items'
    )";
    $stmt = $conn->prepare($checkTable);
    $stmt->execute();
    $tableExists = $stmt->fetchColumn();
    
    if (!$tableExists) {
        echo "<div class='error'>
            <strong>✗ Table does not exist</strong><br>
            The <code>menu_items</code> table does not exist. Please run <code>database_content_tables.sql</code> first.
        </div>";
    } else {
        // Check if menu items already exist
        $checkItems = "SELECT COUNT(*) FROM menu_items";
        $stmt = $conn->prepare($checkItems);
        $stmt->execute();
        $itemCount = $stmt->fetchColumn();
        
        if ($itemCount > 0) {
            echo "<div class='info'>
                <strong>✓ Menu items already exist</strong><br>
                There are already {$itemCount} menu item(s) in the database. No migration needed.
            </div>";
        } else {
            // Insert default top row menu items
            $topItems = [
                ['ໜ້າຫຼັກ', '🏠', 'index.html', 'top', 1],
                ['ກິດຈະກຳ', '📌', 'activities.html', 'top', 2],
                ['ເອກະສານ', '📄', 'documents.html', 'top', 3],
                ['ວິທະຍາໄລຄູ', '🏫', 'teacher-college.html', 'top', 4],
                ['ຜູ້ເຮັດວຽກສຶກສານິເທດພາຍໃນ', '👨‍🏫', 'internal-worker.html', 'top', 5]
            ];
            
            // Insert default bottom row menu items
            $bottomItems = [
                ['ຫ້ອງການສຶກສາທິການເເລະກິລາເມືອງ', '🏛', 'district-education.html', 'bottom', 1],
                ['ພະເເນກສຶກສາທິການເເລະກິລາແຂວງ', '🗺', 'province-activities.html', 'bottom', 2],
                ['CPD', '🎓', 'cpd.html', 'bottom', 3]
            ];
            
            $sql = "INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active) 
                    VALUES (:menu_text, :menu_icon, :menu_url, :menu_row, :display_order, TRUE)";
            $stmt = $conn->prepare($sql);
            
            $inserted = 0;
            foreach (array_merge($topItems, $bottomItems) as $item) {
                try {
                    $stmt->execute([
                        ':menu_text' => $item[0],
                        ':menu_icon' => $item[1],
                        ':menu_url' => $item[2],
                        ':menu_row' => $item[3],
                        ':display_order' => $item[4]
                    ]);
                    $inserted++;
                } catch(PDOException $e) {
                    // Skip if duplicate
                    if (strpos($e->getMessage(), 'duplicate') === false) {
                        throw $e;
                    }
                }
            }
            
            echo "<div class='success'>
                <strong>✓ Migration successful!</strong><br>
                Inserted {$inserted} default menu items into the database.
            </div>";
        }
    }
    
} catch(PDOException $e) {
    echo "<div class='error'>
        <strong>✗ Migration failed</strong><br>
        Error: " . htmlspecialchars($e->getMessage()) . "<br><br>
        <strong>Manual fix:</strong> Run the SQL commands from <code>migration_populate_menu_items.sql</code> in pgAdmin or your PostgreSQL client.
    </div>";
}

echo "<div class='info' style='margin-top: 30px;'>
    <strong>Next steps:</strong><br>
    1. The menu items are now available in the admin panel<br>
    2. Go to Admin → Manage Menu Items to edit or add more menu items<br>
    3. The navbar will automatically load menu items from the database
</div>";

echo "</body></html>";
?>

