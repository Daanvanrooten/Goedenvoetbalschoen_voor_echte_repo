<?php
session_start();
require_once '../../config/db_connection.php';

header('Content-Type: application/json');

// Check of sessie bestaat
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

// Alleen admins mogen deze data ophalen
if (!isset($_SESSION['user']['role_id']) || $_SESSION['user']['role_id'] != 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Geen admin toegang']);
    exit;
}

try {
    $conn = getDbConnection();
    // Haal alle leden op inclusief telefoonnummer
    $ledenStmt = $conn->query("SELECT user_id, first_name, last_name, email, telefoonnummer, username, role_id FROM users");
    $leden = $ledenStmt->fetchAll();
    $aantalLeden = count($leden);

    // Haal alle open taken op (is_active is niet 0/null)
    $takenStmt = $conn->query("SELECT task_id, title, description, start_time, end_time, is_active FROM tasks WHERE is_active IS NULL OR is_active != 0");
    $taken = $takenStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'leden' => $leden,
        'aantalLeden' => $aantalLeden,
        'open_taken' => $taken
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
