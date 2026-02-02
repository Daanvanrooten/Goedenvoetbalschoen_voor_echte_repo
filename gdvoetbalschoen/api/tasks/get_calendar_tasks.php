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
// Als admin: alle taken
// Als user: alleen taken waar deze user aan toegewezen is
if ($isAdmin) {
    $stmt = $pdo->prepare("
        SELECT 
            ts.slot_id,
            ts.slot_date,
            ts.start_time,
            ts.end_time,
            t.title,
            t.task_id,
            tc.color_hex,
            t.frequency
        FROM task_slots ts
        JOIN tasks t ON t.task_id = ts.task_id
        LEFT JOIN task_categories tc ON tc.category_id = t.category_id
        WHERE ts.slot_date BETWEEN :start AND :end
        AND t.is_active = 1
        ORDER BY ts.slot_date, ts.start_time
    ");
    $stmt->execute([
        ':start' => $start,
        ':end'   => $end
    ]);
} else {
    // User: alleen taken waar ze aan toegewezen zijn
    $stmt = $pdo->prepare("
        SELECT 
            ts.slot_id,
            ts.slot_date,
            ts.start_time,
            ts.end_time,
            t.title,
            t.task_id,
            tc.color_hex,
            t.frequency
        FROM task_slots ts
        JOIN tasks t ON t.task_id = ts.task_id
        LEFT JOIN task_categories tc ON tc.category_id = t.category_id
        INNER JOIN task_registrations tr ON tr.slot_id = ts.slot_id
        WHERE ts.slot_date BETWEEN :start AND :end
        AND t.is_active = 1
        AND tr.user_id = :user_id
        ORDER BY ts.slot_date, ts.start_time
    ");
    $stmt->execute([
        ':start' => $start,
        ':end'   => $end,
        ':user_id' => $currentUserId
    ]);
}

$tasksByDate = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $date = $row['slot_date'];
    $tasksByDate[$date][] = [
        'title' => $row['title'],
        'start' => substr($row['start_time'], 0, 5),
        'end'   => substr($row['end_time'], 0, 5),
        'color' => $row['color_hex'],
        'slot_id' => $row['slot_id'],
        'task_id' => $row['task_id'],
        'frequency' => $row['frequency']
    ];
}

// Alle taken worden nu direct uit task_slots gehaald
// De herhalings-logica is al verwerkt bij het aanmaken van de taak
// Er zijn dus alleen expliciete slots in de database tot de einddatum

header('Content-Type: application/json');
echo json_encode($tasksByDate);
