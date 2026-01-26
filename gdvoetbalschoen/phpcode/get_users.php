<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Check of gebruiker is ingelogd
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

// Optioneel: zoekterm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $conn = getDbConnection();

    if ($search !== '') {
        // Zoek gebruikers op basis van naam of email
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, role_id FROM users WHERE first_name LIKE :search OR last_name LIKE :search OR email LIKE :search ORDER BY first_name, last_name");
        $stmt->execute(['search' => '%' . $search . '%']);
    } else {
        // Haal alle gebruikers op
        $stmt = $conn->query("SELECT user_id, first_name, last_name, email, role_id FROM users ORDER BY first_name, last_name");
    }

    $users = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
