<?php
session_start();
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

// Check of gebruiker is ingelogd
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

$slot_id = isset($_GET['slot_id']) ? intval($_GET['slot_id']) : null;
$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : null;
$slot_date = isset($_GET['slot_date']) ? $_GET['slot_date'] : null;

if (!$slot_id && !$task_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Slot ID of Task ID is verplicht']);
    exit;
}

try {
    $conn = getDbConnection();

    // Haal toegewezen personeel op voor deze slot of task
    if ($slot_id) {
        $stmt = $conn->prepare("
            SELECT u.user_id, u.first_name, u.last_name, u.email, u.role_id
            FROM task_registrations tr
            INNER JOIN users u ON u.user_id = tr.user_id
            WHERE tr.slot_id = ?
            ORDER BY u.first_name, u.last_name
        ");
        $stmt->execute([$slot_id]);
    } else {
        // Voor recurring tasks (task_id): probeer eerst specifieke datum, anders eerste slot
        if ($slot_date) {
            // Probeer eerst de specifieke datum
            $stmt = $conn->prepare("
                SELECT DISTINCT u.user_id, u.first_name, u.last_name, u.email, u.role_id
                FROM task_registrations tr
                INNER JOIN task_slots ts ON ts.slot_id = tr.slot_id
                INNER JOIN users u ON u.user_id = tr.user_id
                WHERE ts.task_id = ? AND ts.slot_date = ?
                ORDER BY u.first_name, u.last_name
            ");
            $stmt->execute([$task_id, $slot_date]);
            $tempAssigned = $stmt->fetchAll();
            
            // Als er niemand op deze specifieke datum is, haal het eerste slot op als voorbeeld
            if (empty($tempAssigned)) {
                $stmt = $conn->prepare("
                    SELECT DISTINCT u.user_id, u.first_name, u.last_name, u.email, u.role_id
                    FROM task_registrations tr
                    INNER JOIN task_slots ts ON ts.slot_id = tr.slot_id
                    INNER JOIN users u ON u.user_id = tr.user_id
                    WHERE ts.task_id = ?
                    ORDER BY u.first_name, u.last_name
                    LIMIT 10
                ");
                $stmt->execute([$task_id]);
                $assigned = $stmt->fetchAll();
            } else {
                $assigned = $tempAssigned;
            }
        } else {
            // Zonder datum filter
            $stmt = $conn->prepare("
                SELECT DISTINCT u.user_id, u.first_name, u.last_name, u.email, u.role_id
                FROM task_registrations tr
                INNER JOIN task_slots ts ON ts.slot_id = tr.slot_id
                INNER JOIN users u ON u.user_id = tr.user_id
                WHERE ts.task_id = ?
                ORDER BY u.first_name, u.last_name
            ");
            $stmt->execute([$task_id]);
            $assigned = $stmt->fetchAll();
        }
    }
    if (!isset($assigned)) {
        $assigned = $stmt->fetchAll();
    }

    echo json_encode([
        'success' => true,
        'assigned' => $assigned
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
