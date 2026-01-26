<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}


$user_id = $_SESSION['user']['id'];
$title = trim($_POST['taaknaam'] ?? '');
$category = $_POST['categorie'] ?? null;
$date = $_POST['datum'] ?? null;
$start_time = $_POST['start_time'] ?? null;
$end_time = $_POST['end_time'] ?? null;
$herhaling = $_POST['herhaling'] ?? 'eenmalig';
$maxleden = $_POST['maxleden'] ?? null;
$beschrijving = trim($_POST['beschrijving'] ?? '');
// Personeel (array van user_id's)
$personeel = isset($_POST['personeel']) ? (array)$_POST['personeel'] : [];

// Nieuwe velden voor dag/week/maand/jaar
$day = isset($_POST['day']) ? intval($_POST['day']) : null;
$week = isset($_POST['week']) ? intval($_POST['week']) : null;
$month = isset($_POST['month']) ? intval($_POST['month']) : null;
$year = isset($_POST['year']) ? intval($_POST['year']) : null;

// Validatie
$errors = [];
if (empty($title)) $errors[] = 'Taaknaam is verplicht';
if (empty($category)) $errors[] = 'Categorie is verplicht';
if (empty($date)) $errors[] = 'Datum is verplicht';
if (empty($start_time)) $errors[] = 'Start tijd is verplicht';
if (empty($end_time)) $errors[] = 'Eind tijd is verplicht';
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getDbConnection();
    // Insert task met nieuwe dag/week/maand/jaar velden
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, category_id, is_active, created_at, start_time, end_time, day, week, month, year) VALUES (?, ?, ?, 1, NOW(), ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $beschrijving, $category, $start_time, $end_time, $day, $week, $month, $year]);
    $task_id = $conn->lastInsertId();

    // Voeg slot toe met start en eind uur
    $stmtSlot = $conn->prepare("INSERT INTO task_slots (task_id, slot_date, start_time, end_time, capacity, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmtSlot->execute([$task_id, $date, $start_time, $end_time, $maxleden ?? 1]);
    $slot_id = $conn->lastInsertId();

    // Personeel koppelen aan taak (task_registrations)
    if (!empty($personeel)) {
        $stmtReg = $conn->prepare("INSERT INTO task_registrations (slot_id, user_id) VALUES (?, ?)");
        foreach ($personeel as $userId) {
            if (!empty($userId)) {
                $stmtReg->execute([$slot_id, $userId]);
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Taak succesvol opgeslagen!', 'task_id' => $task_id, 'category_id' => $category]);
} catch (PDOException $e) {
    error_log('Taak opslaan fout: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
