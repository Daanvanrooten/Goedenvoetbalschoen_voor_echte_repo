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
        t.task_id,
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
        'start' => substr($row['start_time'], 0, 5),
        'end'   => substr($row['end_time'], 0, 5),
        'color' => $row['color_hex'],
        'slot_id' => $row['slot_id'],
        'task_id' => $row['task_id'],
        'frequency' => $row['frequency'],
        'capacity' => $row['capacity'],
        'registered_count' => $row['registered_count'],
        'user_is_registered' => $row['user_is_registered'] > 0
    ];
}

// 2. Haal alle maandelijkse taken op
// Zowel admin als user kunnen alle maandelijkse taken zien
$monthlyStmt = $pdo->prepare("
    SELECT t.*, tc.color_hex
    FROM tasks t
    LEFT JOIN task_categories tc ON tc.category_id = t.category_id
    WHERE t.frequency = 'MONTHLY' AND t.is_active = 1
");
$monthlyStmt->execute();

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
                    'task_id' => $task['task_id'],
                    'frequency' => 'MONTHLY'
                ];
            }
        }
        // Volgende maand
        $current->modify('first day of next month');
    }
}

// 3. Haal alle wekelijkse taken op
if ($isAdmin) {
    $weeklyStmt = $pdo->prepare("
        SELECT t.*, tc.color_hex, ts.slot_date as first_slot_date
        FROM tasks t
        LEFT JOIN task_categories tc ON tc.category_id = t.category_id
        LEFT JOIN task_slots ts ON ts.task_id = t.task_id
        WHERE t.frequency = 'WEEKLY' AND t.is_active = 1
        GROUP BY t.task_id
    ");
    $weeklyStmt->execute();
} else {
    $weeklyStmt = $pdo->prepare("
        SELECT DISTINCT t.*, tc.color_hex, ts.slot_date as first_slot_date
        FROM tasks t
        LEFT JOIN task_categories tc ON tc.category_id = t.category_id
        INNER JOIN task_slots ts ON ts.task_id = t.task_id
        INNER JOIN task_registrations tr ON tr.slot_id = ts.slot_id
        WHERE t.frequency = 'WEEKLY' AND t.is_active = 1
        AND tr.user_id = :user_id
        GROUP BY t.task_id
    ");
    $weeklyStmt->execute([':user_id' => $currentUserId]);
}

while ($task = $weeklyStmt->fetch(PDO::FETCH_ASSOC)) {
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $current = clone $startDate;

    // Gebruik de eerste slot_date om de dag van de week te bepalen
    $firstSlotDate = isset($task['first_slot_date']) ? new DateTime($task['first_slot_date']) : null;
    $weekDay = $firstSlotDate ? (int)$firstSlotDate->format('N') : null; // 1=maandag, 7=zondag

    while ($current <= $endDate) {
        // Check of deze dag van de week klopt
        if ($weekDay !== null && (int)$current->format('N') === $weekDay) {
            $dateStr = $current->format('Y-m-d');
            $alreadyExists = false;
            if (isset($tasksByDate[$dateStr])) {
                foreach ($tasksByDate[$dateStr] as $existing) {
                    // Skip als deze taak al bestaat op deze datum (via slot of frequency)
                    if ($existing['title'] === $task['title']) {
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
                    'task_id' => $task['task_id'],
                    'frequency' => 'WEEKLY'
                ];
            }
        }
        $current->modify('+1 day');
    }
}

// 4. Haal alle dagelijkse taken op
if ($isAdmin) {
    $dailyStmt = $pdo->prepare("
        SELECT t.*, tc.color_hex
        FROM tasks t
        LEFT JOIN task_categories tc ON tc.category_id = t.category_id
        WHERE t.frequency = 'DAILY' AND t.is_active = 1
    ");
    $dailyStmt->execute();
} else {
    $dailyStmt = $pdo->prepare("
        SELECT DISTINCT t.*, tc.color_hex
        FROM tasks t
        LEFT JOIN task_categories tc ON tc.category_id = t.category_id
        INNER JOIN task_slots ts ON ts.task_id = t.task_id
        INNER JOIN task_registrations tr ON tr.slot_id = ts.slot_id
        WHERE t.frequency = 'DAILY' AND t.is_active = 1
        AND tr.user_id = :user_id
    ");
    $dailyStmt->execute([':user_id' => $currentUserId]);
}

while ($task = $dailyStmt->fetch(PDO::FETCH_ASSOC)) {
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $current = clone $startDate;
    while ($current <= $endDate) {
        $dateStr = $current->format('Y-m-d');
        $alreadyExists = false;
        if (isset($tasksByDate[$dateStr])) {
            foreach ($tasksByDate[$dateStr] as $existing) {
                // Skip als deze taak al bestaat op deze datum (via slot of frequency)
                if ($existing['title'] === $task['title']) {
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
                'task_id' => $task['task_id'],
                'frequency' => 'DAILY'
            ];
        }
        $current->modify('+1 day');
    }
}

header('Content-Type: application/json');
echo json_encode($tasksByDate);
