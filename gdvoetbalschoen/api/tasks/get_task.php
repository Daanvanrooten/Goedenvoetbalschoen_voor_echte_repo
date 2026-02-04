<?php
session_start();
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

$conn = getDbConnection();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : null;

$sqlGetTask = "SELECT * FROM tasks WHERE task_id = :task_id";
$sqlGetSlotID = "SELECT slot_id FROM task_slots WHERE task_id = :task_id LIMIT 1";

$stmtTasks = $conn->prepare($sqlGetTask);
$stmtTasks->execute([':task_id' => $task_id]);
$Tasks = $stmtTasks->fetch(PDO::FETCH_ASSOC);

$stmtSlotID = $conn->prepare($sqlGetSlotID);
$stmtSlotID->execute([':task_id' => $task_id]);
$SlotID = $stmtSlotID->fetch(PDO::FETCH_ASSOC);

$Date = sprintf("%04d-%02d-%02d", $Tasks["year"], $Tasks["month"], $Tasks["day"]);

echo json_encode(['success' => true, 'message' => 'Got task!', 
    'title' => $Tasks["title"],
    'category' => $Tasks["category_id"],
    'timeStart' => $Tasks["start_time"],
    'timeEnd' => $Tasks["end_time"],
    'SlotID' => $SlotID["slot_id"],
    ]);
?>
