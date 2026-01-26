<?php
require_once '../phpcode/db_connection.php';
$pdo = getDbConnection();

// Bepaal maandbereik (voorbeeld: januari 2026)
$start = isset($_GET['start']) ? $_GET['start'] : '2026-01-01';
$end = isset($_GET['end']) ? $_GET['end'] : '2026-01-31';

$stmt = $pdo->prepare("
    SELECT 
        ts.slot_id,
        ts.slot_date,
        ts.start_time,
        ts.end_time,
        t.title,
        tc.color_hex
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

$tasksByDate = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $date = $row['slot_date'];
    $tasksByDate[$date][] = [
        'title' => $row['title'],
        'start' => substr($row['start_time'], 0, 5),
        'end'   => substr($row['end_time'], 0, 5),
        'color' => $row['color_hex'],
        'slot_id' => $row['slot_id']
    ];
}
header('Content-Type: application/json');
echo json_encode($tasksByDate);
