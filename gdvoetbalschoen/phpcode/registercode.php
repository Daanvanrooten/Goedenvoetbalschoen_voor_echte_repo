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
$voornaam = trim($_POST['voornaam'] ?? '');
$achternaam = trim($_POST['achternaam'] ?? '');
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validatie
$errors = [];

if (empty($voornaam)) {
    $errors[] = 'Voornaam is verplicht';
}

if (empty($achternaam)) {
    $errors[] = 'Achternaam is verplicht';
}

if (empty($email)) {
    $errors[] = 'Email is verplicht';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ongeldig email adres';
}

if (empty($username)) {
    $errors[] = 'Gebruikersnaam is verplicht';
} elseif (strlen($username) < 3) {
    $errors[] = 'Gebruikersnaam moet minimaal 3 karakters zijn';
}

if (empty($password)) {
    $errors[] = 'Wachtwoord is verplicht';
} elseif (strlen($password) < 6) {
    $errors[] = 'Wachtwoord moet minimaal 6 karakters zijn';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Check of email al bestaat
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email adres is al in gebruik']);
        exit;
    }
    
    // Check of username al bestaat
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Gebruikersnaam is al in gebruik']);
        exit;
    }
    
    // Hash wachtwoord
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Standaard role_id voor nieuwe gebruikers (1 = regular user)
    $roleId = 1;
    
    // Insert nieuwe gebruiker
    $stmt = $conn->prepare("
        INSERT INTO users 
        (role_id, first_name, last_name, email, username, password_hash, is_active, is_email_verified, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 1, 0, NOW())
    ");
    
    $stmt->execute([
        $roleId,
        $voornaam,
        $achternaam,
        $email,
        $username,
        $passwordHash
    ]);
    
    $userId = $conn->lastInsertId();
    
    // Haal gebruiker op voor sessie
    $stmt = $conn->prepare("
        SELECT user_id, first_name, last_name, email, username, role_id 
        FROM users 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    // Sla gebruiker op in sessie
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
        'message' => 'Account succesvol aangemaakt!',
        'redirect' => 'agenda.php'
    ]);
    
} catch (PDOException $e) {
    error_log("Registratie fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
?>
