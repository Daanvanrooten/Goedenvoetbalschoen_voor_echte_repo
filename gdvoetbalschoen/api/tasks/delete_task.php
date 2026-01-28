<?php
session_start();
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

// Check of gebruiker is ingelogd en admin is
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

if (!isset($_SESSION['user']['role_id']) || $_SESSION['user']['role_id'] != 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Alleen admins kunnen taken verwijderen']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}

$task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : null;
$slot_id = isset($_POST['slot_id']) ? intval($_POST['slot_id']) : null;

if (!$task_id && !$slot_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Taak ID of Slot ID is verplicht']);
    exit;
}

try {
    $conn = getDbConnection();
    $conn->beginTransaction();

    if ($slot_id) {
        // Verwijder specifieke slot en bijbehorende registraties
        $stmt = $conn->prepare("DELETE FROM task_registrations WHERE slot_id = ?");
        $stmt->execute([$slot_id]);

        $stmt = $conn->prepare("DELETE FROM task_slots WHERE slot_id = ?");
        $stmt->execute([$slot_id]);

        $message = 'Slot succesvol verwijderd';
    } else {
        // Verwijder hele taak met alle slots en registraties
        // Eerst alle registraties van alle slots van deze taak
        $stmt = $conn->prepare("DELETE tr FROM task_registrations tr INNER JOIN task_slots ts ON tr.slot_id = ts.slot_id WHERE ts.task_id = ?");
        $stmt->execute([$task_id]);

        // Dan alle slots
        $stmt = $conn->prepare("DELETE FROM task_slots WHERE task_id = ?");
        $stmt->execute([$task_id]);

        // Dan de taak zelf (of set is_active = 0 voor soft delete)
        $stmt = $conn->prepare("UPDATE tasks SET is_active = 0 WHERE task_id = ?");
        $stmt->execute([$task_id]);

        $message = 'Taak succesvol verwijderd';
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => $message]);
} catch (PDOException $e) {
    $conn->rollBack();
    error_log('Taak verwijderen fout: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
