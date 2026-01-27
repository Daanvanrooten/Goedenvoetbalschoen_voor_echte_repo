<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

// Check of gebruiker is ingelogd
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

$slot_id = isset($_GET['slot_id']) ? intval($_GET['slot_id']) : null;

if (!$slot_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Slot ID is verplicht']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Haal toegewezen personeel op voor deze slot
    $stmt = $conn->prepare("
        SELECT u.user_id, u.first_name, u.last_name, u.email, u.role_id
        FROM task_registrations tr
        INNER JOIN users u ON u.user_id = tr.user_id
        WHERE tr.slot_id = ?
        ORDER BY u.first_name, u.last_name
    ");
    $stmt->execute([$slot_id]);
    $assigned = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'assigned' => $assigned
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
