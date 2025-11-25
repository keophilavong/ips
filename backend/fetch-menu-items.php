<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

try {
    $is_admin = isset($_SESSION['admin_id']);
    
    $sql = "SELECT menu_id, menu_text, menu_icon, menu_url, menu_row, display_order, is_active, created_at, updated_at 
            FROM menu_items";
    
    if (!$is_admin) {
        $sql .= " WHERE is_active = TRUE";
    }
    
    $sql .= " ORDER BY menu_row, display_order ASC, created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by row
    $grouped = [
        'top' => [],
        'bottom' => []
    ];
    
    foreach ($menuItems as $item) {
        $row = $item['menu_row'] ?? 'top';
        if (!isset($grouped[$row])) {
            $grouped[$row] = [];
        }
        $grouped[$row][] = $item;
    }
    
    echo json_encode($grouped, JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

