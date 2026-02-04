<?php
session_start();
require_once '../../config/db_connection.php';

// Check of gebruiker is ingelogd
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

$pdo = getDbConnection();
$currentUserId = $_SESSION['user']['id'];
$isAdmin = isset($_SESSION['user']['role_id']) && $_SESSION['user']['role_id'] == 2;

// Bepaal maandbereik (voorbeeld: januari 2026)
$start = isset($_GET['start']) ? $_GET['start'] : '2026-01-01';
$end = isset($_GET['end']) ? $_GET['end'] : '2026-01-31';


// 1. Haal alle slots in het bereik op
// Zowel admin als user kunnen alle taken zien
$stmt = $pdo->prepare("
    SELECT 
        ts.slot_id,
        ts.slot_date,
        ts.start_time,
        ts.end_time,
        ts.capacity,
        t.title,
        t.description,
        t.task_id,
        t.category_id,
        tc.category_name,
        tc.color_hex,
        t.frequency,
        (SELECT COUNT(*) FROM task_registrations WHERE slot_id = ts.slot_id) as registered_count,
        (SELECT COUNT(*) FROM task_registrations WHERE slot_id = ts.slot_id AND user_id = :user_id) as user_is_registered
    FROM task_slots ts
    JOIN tasks t ON t.task_id = ts.task_id
    LEFT JOIN task_categories tc ON tc.category_id = t.category_id
    WHERE ts.slot_date BETWEEN :start AND :end
    AND t.is_active = 1
    ORDER BY ts.slot_date, ts.start_time
");
$stmt->execute([
    ':start' => $start,
    ':end'   => $end,
    ':user_id' => $currentUserId
]);

$tasksByDate = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $date = $row['slot_date'];
    $tasksByDate[$date][] = [
        'title' => $row['title'],
        'description' => $row['description'],
        'start' => substr($row['start_time'], 0, 5),
        'end'   => substr($row['end_time'], 0, 5),
        'color' => $row['color_hex'],
        'slot_id' => $row['slot_id'],
        'task_id' => $row['task_id'],
        'category_id' => $row['category_id'],
        'category_name' => $row['category_name'],
        'frequency' => $row['frequency'],
        'capacity' => $row['capacity'],
        'registered_count' => $row['registered_count'],
        'user_is_registered' => $row['user_is_registered'] > 0
    ];
}

// Alle taken worden nu via expliciete slots opgehaald
// Er is geen dynamische generatie meer nodig omdat create_task.php
// automatisch slots aanmaakt tot de einddatum voor herhalende taken

header('Content-Type: application/json');
echo json_encode($tasksByDate);
