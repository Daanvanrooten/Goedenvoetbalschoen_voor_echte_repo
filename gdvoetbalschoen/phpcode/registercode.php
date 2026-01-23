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
$telefoonnummer = trim($_POST['telefoonnummer'] ?? '');
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

if (empty($telefoonnummer)) {
    $errors[] = 'Telefoonnummer is verplicht';
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
        (role_id, first_name, last_name, email, telefoonnummer, username, password_hash, is_active, is_email_verified, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 1, 0, NOW())
    ");

    $stmt->execute([
        $roleId,
        $voornaam,
        $achternaam,
        $email,
        $telefoonnummer,
        $username,
        $passwordHash
    ]);

    $userId = $conn->lastInsertId();

    // Genereer 6-cijferige verificatiecode
    $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Sla verificatiecode op
    $stmt = $conn->prepare("
        INSERT INTO email_verifications (user_id, token, created_at, expires_at) 
        VALUES (?, ?, NOW(), ?)
    ");
    $stmt->execute([$userId, $verificationCode, $expiresAt]);

    // Verstuur email met verificatiecode
    require_once __DIR__ . '/email_functions.php';
    $emailSent = sendVerificationEmail($email, "$voornaam $achternaam", $verificationCode);

    // Log voor debug (blijft ook werken als email faalt)
    $debugMessage = "=== EMAIL VERIFICATIE ===\n";
    $debugMessage .= "Tijd: " . date('Y-m-d H:i:s') . "\n";
    $debugMessage .= "Naar: $email\n";
    $debugMessage .= "Gebruiker: $voornaam $achternaam\n";
    $debugMessage .= "Verificatiecode: $verificationCode\n";
    $debugMessage .= "Geldig tot: $expiresAt\n";
    $debugMessage .= "Email verzonden: " . ($emailSent ? 'JA' : 'NEE') . "\n";
    $debugMessage .= "========================\n\n";
    file_put_contents(__DIR__ . '/verification_codes.txt', $debugMessage, FILE_APPEND);

    // Sla tijdelijk gebruiker info op in sessie voor verificatie pagina
    $_SESSION['pending_verification'] = [
        'user_id' => $userId,
        'email' => $email,
        'first_name' => $voornaam,
        'last_name' => $achternaam
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Account aangemaakt! Controleer je email voor de verificatiecode.',
        'redirect' => 'verify_email.php',
        'verification_code' => $verificationCode // Tijdelijk voor testing, verwijder later
    ]);
} catch (PDOException $e) {
    error_log("Registratie fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
