<?php
session_start();
require_once '../phpcode/db_connection.php';

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
        'frequency' => $row['frequency']
    ];
}

// 2. Haal alle maandelijkse taken op
// Als admin: alle maandelijkse taken
// Als user: alleen maandelijkse taken waar ze aan toegewezen zijn
if ($isAdmin) {
    $monthlyStmt = $pdo->prepare("
        SELECT t.*, tc.color_hex
        FROM tasks t
        LEFT JOIN task_categories tc ON tc.category_id = t.category_id
        WHERE t.frequency = 'MONTHLY' AND t.is_active = 1
    ");
    $monthlyStmt->execute();
} else {
    // Voor users: alleen maandelijkse taken waar ze aan toegewezen zijn
    // Dit is complex omdat maandelijkse taken meerdere slots kunnen hebben
    $monthlyStmt = $pdo->prepare("
        SELECT DISTINCT t.*, tc.color_hex
        FROM tasks t
        LEFT JOIN task_categories tc ON tc.category_id = t.category_id
        INNER JOIN task_slots ts ON ts.task_id = t.task_id
        INNER JOIN task_registrations tr ON tr.slot_id = ts.slot_id
        WHERE t.frequency = 'MONTHLY' AND t.is_active = 1
        AND tr.user_id = :user_id
    ");
    $monthlyStmt->execute([':user_id' => $currentUserId]);
}

while ($task = $monthlyStmt->fetch(PDO::FETCH_ASSOC)) {
    // Voor elke maand in het bereik
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $current = clone $startDate;
    while ($current <= $endDate) {
        $year = (int)$current->format('Y');
        $month = (int)$current->format('m');
        $day = (int)$task['day'];
        // Check of deze dag bestaat in deze maand
        if (checkdate($month, $day, $year)) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
            // Alleen toevoegen als er nog geen slot is voor deze taak op deze dag
            $alreadyExists = false;
            if (isset($tasksByDate[$dateStr])) {
                foreach ($tasksByDate[$dateStr] as $existing) {
                    if (isset($existing['slot_id']) && isset($task['task_id']) && $existing['frequency'] === 'MONTHLY' && $existing['title'] === $task['title']) {
                        $alreadyExists = true;
                        break;
                    }
                }
            }
            if (!$alreadyExists) {
                $tasksByDate[$dateStr][] = [
                    'title' => $task['title'],
                    'start' => isset($task['start_time']) ? substr($task['start_time'], 0, 5) : '',
                    'end'   => isset($task['end_time']) ? substr($task['end_time'], 0, 5) : '',
                    'color' => $task['color_hex'],
                    'slot_id' => null,
                    'frequency' => 'MONTHLY'
                ];
            }
        }
        // Volgende maand
        $current->modify('first day of next month');
    }
}

header('Content-Type: application/json');
echo json_encode($tasksByDate);
