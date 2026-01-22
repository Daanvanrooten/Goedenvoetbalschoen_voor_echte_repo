<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}

// Haal POST data op
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validatie
$errors = [];

if (empty($username)) {
    $errors[] = 'Gebruikersnaam is verplicht';
}

if (empty($password)) {
    $errors[] = 'Wachtwoord is verplicht';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Haal gebruiker op met username
    $stmt = $conn->prepare("
        SELECT user_id, role_id, first_name, last_name, email, username, password_hash, is_active 
        FROM users 
        WHERE username = ?
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // Check of gebruiker bestaat
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Gebruikersnaam of wachtwoord is onjuist']);
        exit;
    }
    
    // Check of account actief is
    if (!$user['is_active']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Account is gedeactiveerd. Neem contact op met de beheerder.']);
        exit;
    }
    
    // Verifieer wachtwoord
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Gebruikersnaam of wachtwoord is onjuist']);
        exit;
    }
    
    // Login succesvol - sla gebruiker op in sessie
    $_SESSION['user'] = [
        'id' => $user['user_id'],
        'voornaam' => $user['first_name'],
        'achternaam' => $user['last_name'],
        'email' => $user['email'],
        'username' => $user['username'],
        'role_id' => $user['role_id']
    ];
    
    echo json_encode([
        'success' => true, 
        'message' => 'Login succesvol!',
        'redirect' => 'agenda.php'
    ]);
    
} catch (PDOException $e) {
    error_log("Login fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
?>
