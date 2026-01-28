<?php
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

$slot_id = isset($_GET['slot_id']) ? intval($_GET['slot_id']) : 0;
if (!$slot_id) {
    echo json_encode(['success' => false, 'message' => 'Geen slot_id opgegeven']);
    exit;
}

try {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT u.user_id, u.first_name, u.last_name, u.username, u.email FROM task_registrations tr JOIN users u ON tr.user_id = u.user_id WHERE tr.slot_id = ?");
    $stmt->execute([$slot_id]);
    $leden = $stmt->fetchAll();
    echo json_encode(['success' => true, 'leden' => $leden]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
