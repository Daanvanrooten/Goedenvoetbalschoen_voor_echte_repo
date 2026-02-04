<?php
session_start();
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

// Alleen admins mogen gebruikers verwijderen
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Geen toegang. Alleen admins kunnen gebruikers verwijderen.']);
    exit;
}

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geen geldige gebruiker geselecteerd']);
    exit;
}

// Voorkom dat de admin zichzelf verwijdert
if ($user_id === $_SESSION['user']['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Je kunt jezelf niet verwijderen']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Haal eerst de gebruikersinformatie op voor feedback
    $stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Gebruiker niet gevonden']);
        exit;
    }
    
    // Verwijder de gebruiker
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Gebruiker ' . $user['first_name'] . ' ' . $user['last_name'] . ' succesvol verwijderd'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
