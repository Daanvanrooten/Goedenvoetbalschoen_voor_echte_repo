<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

// Check of gebruiker is ingelogd en admin is
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

if (!isset($_SESSION['user']['role_id']) || $_SESSION['user']['role_id'] != 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Alleen admins kunnen taken bewerken']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}

$task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : null;
$slot_id = isset($_POST['slot_id']) ? intval($_POST['slot_id']) : null;
$title = isset($_POST['title']) ? trim($_POST['title']) : null;
$start_time = isset($_POST['start_time']) ? $_POST['start_time'] : null;
$end_time = isset($_POST['end_time']) ? $_POST['end_time'] : null;
$slot_date = isset($_POST['slot_date']) ? $_POST['slot_date'] : null;

if (!$task_id && !$slot_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Taak ID of Slot ID is verplicht']);
    exit;
}

try {
    $conn = getDbConnection();

    // Update task title als gegeven
    if ($task_id && $title) {
        $stmt = $conn->prepare("UPDATE tasks SET title = ? WHERE task_id = ?");
        $stmt->execute([$title, $task_id]);
    }

    // Update slot times als gegeven
    if ($slot_id && ($start_time || $end_time || $slot_date)) {
        $updates = [];
        $params = [];

        if ($start_time) {
            $updates[] = "start_time = ?";
            $params[] = $start_time;
        }
        if ($end_time) {
            $updates[] = "end_time = ?";
            $params[] = $end_time;
        }
        if ($slot_date) {
            $updates[] = "slot_date = ?";
            $params[] = $slot_date;
        }

        if (!empty($updates)) {
            $params[] = $slot_id;
            $stmt = $conn->prepare("UPDATE task_slots SET " . implode(', ', $updates) . " WHERE slot_id = ?");
            $stmt->execute($params);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Taak succesvol bijgewerkt']);
} catch (PDOException $e) {
    error_log('Taak updaten fout: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
