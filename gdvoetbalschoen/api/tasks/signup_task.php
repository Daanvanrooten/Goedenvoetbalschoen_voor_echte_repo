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
    
    // Controleer of de slot bestaat en haal capaciteit op
    $stmt = $conn->prepare("
        SELECT ts.capacity, 
               (SELECT COUNT(*) FROM task_registrations WHERE slot_id = ts.slot_id) as current_registrations
        FROM task_slots ts
        WHERE ts.slot_id = ?
    ");
    $stmt->execute([$slot_id]);
    $slot = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$slot) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Taak niet gevonden']);
        exit;
    }
    
    // Controleer of er nog plek is
    if ($slot['current_registrations'] >= $slot['capacity']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Taak is al vol']);
        exit;
    }
    
    // Controleer of gebruiker al is ingeschreven
    $checkStmt = $conn->prepare("SELECT registration_id FROM task_registrations WHERE slot_id = ? AND user_id = ?");
    $checkStmt->execute([$slot_id, $user_id]);
    
    if ($checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Je bent al ingeschreven voor deze taak']);
        exit;
    }
    
    // Schrijf gebruiker in
    $insertStmt = $conn->prepare("INSERT INTO task_registrations (slot_id, user_id, registered_at) VALUES (?, ?, NOW())");
    $insertStmt->execute([$slot_id, $user_id]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Je bent succesvol ingeschreven voor deze taak!',
        'registration_id' => $conn->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    error_log('Inschrijven fout: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
