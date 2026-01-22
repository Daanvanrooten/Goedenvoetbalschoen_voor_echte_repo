<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Alleen admins mogen deze data ophalen
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role_id']) || $_SESSION['user']['role_id'] != 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Geen toegang']);
    exit;
}

try {
    $conn = getDbConnection();
    // Haal alle leden op
    $ledenStmt = $conn->query("SELECT user_id, first_name, last_name, email, username, role_id FROM users");
    $leden = $ledenStmt->fetchAll();

    // Haal alle open taken op (is_active = 1)
    $takenStmt = $conn->query("SELECT task_id, title, description, start_date, end_date, is_active FROM tasks WHERE is_active = 1");
    $taken = $takenStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'leden' => $leden,
        'open_taken' => $taken
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
