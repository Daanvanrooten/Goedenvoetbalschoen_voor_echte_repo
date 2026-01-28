<?php
require_once '../../config/db_connection.php';
header('Content-Type: application/json');
try {
    $conn = getDbConnection();
    $stmt = $conn->query('SELECT category_id, name FROM task_categories ORDER BY name');
    $categories = $stmt->fetchAll();
    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
