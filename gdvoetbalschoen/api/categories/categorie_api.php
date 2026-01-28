<?php
session_start();
require_once '../../config/db_connection.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Geen toegang']);
    exit;
}
$action = $_GET['action'] ?? '';
try {
    $conn = getDbConnection();
    if ($action === 'list') {
        $stmt = $conn->query('SELECT category_id, name, color_hex FROM task_categories');
        $categories = $stmt->fetchAll();
        echo json_encode(['success' => true, 'categories' => $categories]);
    } elseif ($action === 'add') {
        $name = $_POST['name'] ?? '';
        $color = $_POST['color_hex'] ?? '#cccccc';
        if (!$name) throw new Exception('Naam verplicht');
        $stmt = $conn->prepare('INSERT INTO task_categories (name, color_hex) VALUES (?, ?)');
        $stmt->execute([$name, $color]);
        echo json_encode(['success' => true]);
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $color = $_POST['color_hex'] ?? '#cccccc';
        if (!$id || !$name) throw new Exception('ID en naam verplicht');
        $stmt = $conn->prepare('UPDATE task_categories SET name = ?, color_hex = ? WHERE category_id = ?');
        $stmt->execute([$name, $color, $id]);
        echo json_encode(['success' => true]);
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        if (!$id) throw new Exception('ID verplicht');
        $stmt = $conn->prepare('DELETE FROM task_categories WHERE category_id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ongeldige actie']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
