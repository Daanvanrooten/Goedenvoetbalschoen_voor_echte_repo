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
$time = $_POST['tijd'] ?? null;
$herhaling = $_POST['herhaling'] ?? 'eenmalig';
$maxleden = $_POST['maxleden'] ?? null;
$beschrijving = trim($_POST['beschrijving'] ?? '');

// Validatie
$errors = [];
if (empty($title)) $errors[] = 'Taaknaam is verplicht';
if (empty($category)) $errors[] = 'Categorie is verplicht';
if (empty($date)) $errors[] = 'Datum is verplicht';
if (empty($time)) $errors[] = 'Tijd is verplicht';
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getDbConnection();
    // Insert task (vereenvoudigd, zonder herhaling/slots)
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, is_active, created_at) VALUES (?, ?, 1, NOW())");
    $stmt->execute([$title, $beschrijving]);
    $task_id = $conn->lastInsertId();
    // Optioneel: koppel aan user, categorie, etc.
    echo json_encode(['success' => true, 'message' => 'Taak succesvol opgeslagen!']);
} catch (PDOException $e) {
    error_log('Taak opslaan fout: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
