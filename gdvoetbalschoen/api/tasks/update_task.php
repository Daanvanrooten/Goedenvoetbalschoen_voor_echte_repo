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
$personeel = isset($_POST['personeel']) ? $_POST['personeel'] : null; // Comma-separated user IDs

if (!$task_id && !$slot_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Taak ID of Slot ID is verplicht']);
    exit;
}

try {
    $conn = getDbConnection();

    // Update task title en times als task_id gegeven is
    if ($task_id && $title) {
        $stmt = $conn->prepare("UPDATE tasks SET title = ? WHERE task_id = ?");
        $stmt->execute([$title, $task_id]);
    }

    // Update task times (voor frequency-based tasks)
    if ($task_id && ($start_time || $end_time)) {
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

        if (!empty($updates)) {
            $params[] = $task_id;
            $stmt = $conn->prepare("UPDATE tasks SET " . implode(', ', $updates) . " WHERE task_id = ?");
            $stmt->execute($params);
        }
    }

    // Update slot times als slot_id gegeven is (voor specifieke slots)
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

    // Update personeel assignments als slot_id gegeven is
    if ($slot_id && $personeel !== null) {
        // Verwijder alle huidige assignments voor deze slot
        $stmt = $conn->prepare("DELETE FROM task_registrations WHERE slot_id = ?");
        $stmt->execute([$slot_id]);

        // Voeg nieuwe assignments toe
        if (!empty($personeel)) {
            $userIds = array_filter(array_map('trim', explode(',', $personeel)));
            if (!empty($userIds)) {
                $stmtReg = $conn->prepare("INSERT INTO task_registrations (slot_id, user_id) VALUES (?, ?)");
                foreach ($userIds as $userId) {
                    if (is_numeric($userId) && $userId > 0) {
                        $stmtReg->execute([$slot_id, intval($userId)]);
                    }
                }
            }
        }
    }

    // Update personeel assignments als task_id gegeven is (recurring tasks)
    if ($task_id && !$slot_id && $personeel !== null) {
        // Haal slots op voor deze task, gefilterd op datum indien opgegeven
        if ($slot_date) {
            $stmt = $conn->prepare("SELECT slot_id FROM task_slots WHERE task_id = ? AND slot_date = ?");
            $stmt->execute([$task_id, $slot_date]);
        } else {
            $stmt = $conn->prepare("SELECT slot_id FROM task_slots WHERE task_id = ?");
            $stmt->execute([$task_id]);
        }
        $slots = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($slots)) {
            // Verwijder ALLEEN huidige assignments voor de geselecteerde slots (niet alle!)
            $placeholders = implode(',', array_fill(0, count($slots), '?'));
            $stmt = $conn->prepare("DELETE FROM task_registrations WHERE slot_id IN ($placeholders)");
            $stmt->execute($slots);

            // Voeg nieuwe assignments toe voor de geselecteerde slots
            if (!empty($personeel)) {
                $userIds = array_filter(array_map('trim', explode(',', $personeel)));
                if (!empty($userIds)) {
                    $stmtReg = $conn->prepare("INSERT INTO task_registrations (slot_id, user_id) VALUES (?, ?)");
                    foreach ($slots as $slotId) {
                        foreach ($userIds as $userId) {
                            if (is_numeric($userId) && $userId > 0) {
                                $stmtReg->execute([$slotId, intval($userId)]);
                            }
                        }
                    }
                }
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Taak succesvol bijgewerkt']);
} catch (PDOException $e) {
    error_log('Taak updaten fout: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
