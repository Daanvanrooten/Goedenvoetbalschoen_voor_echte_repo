<?php
session_start();
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

// Controleer of gebruiker is ingelogd
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
$slot_id = $_POST['slot_id'] ?? null;

// Validatie
if (empty($slot_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Slot ID is verplicht']);
    exit;
}

try {
    $conn = getDbConnection();

    // Controleer of gebruiker is ingeschreven
    $checkStmt = $conn->prepare("SELECT registration_id FROM task_registrations WHERE slot_id = ? AND user_id = ?");
    $checkStmt->execute([$slot_id, $user_id]);
    $registration = $checkStmt->fetch();

    if (!$registration) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Je bent niet ingeschreven voor deze taak']);
        exit;
    }

    // Schrijf gebruiker uit
    $deleteStmt = $conn->prepare("DELETE FROM task_registrations WHERE slot_id = ? AND user_id = ?");
    $deleteStmt->execute([$slot_id, $user_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Je bent succesvol uitgeschreven van deze taak'
    ]);
} catch (PDOException $e) {
    error_log('Uitschrijven fout: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
