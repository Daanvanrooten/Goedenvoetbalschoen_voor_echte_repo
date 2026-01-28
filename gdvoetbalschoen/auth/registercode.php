<?php
// Registratie handler voor nieuwe gebruikers
// Maakt account aan, genereert verificatiecode en verstuurt email

session_start();
require_once '../config/db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}

// Haal formulier data op
$voornaam = trim($_POST['voornaam'] ?? '');
$achternaam = trim($_POST['achternaam'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefoonnummer = trim($_POST['telefoonnummer'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Valideer alle velden
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
    // ===== STAP 3: MAAK DATABASE VERBINDING =====
    $conn = getDbConnection();

    // ===== STAP 4: CHECK OF EMAIL AL BESTAAT =====
    // We willen geen dubbele email adressen in het systeem
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email adres is al in gebruik']);
        exit;
    }

    // ===== STAP 5: CHECK OF USERNAME AL BESTAAT =====
    // Elke gebruiker moet een unieke username hebben
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Gebruikersnaam is al in gebruik']);
        exit;
    }

    // ===== STAP 6: HASH HET WACHTWOORD =====
    // We slaan NOOIT wachtwoorden in plain text op!
    // password_hash() maakt er een onleesbare string van (encryptie)
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Standaard role_id voor nieuwe gebruikers
    // 1 = normale gebruiker, 2 = admin
    $roleId = 1;

    // ===== STAP 7: INSERT NIEUWE GEBRUIKER IN DATABASE =====
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

    // Haal het ID op van de zojuist aangemaakte gebruiker
    $userId = $conn->lastInsertId();

    // ===== STAP 8: GENEREER VERIFICATIECODE =====
    // Maak een random 6-cijferige code (bijv. 123456)
    $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Code is 15 minuten geldig
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // ===== STAP 9: SLA VERIFICATIECODE OP IN DATABASE =====
    $stmt = $conn->prepare("
        INSERT INTO email_verifications (user_id, token, created_at, expires_at) 
        VALUES (?, ?, NOW(), ?)
    ");
    $stmt->execute([$userId, $verificationCode, $expiresAt]);

    // ===== STAP 10: VERSTUUR EMAIL MET CODE =====
    require_once __DIR__ . '/email_sender.php';
    $emailSent = sendVerificationEmail($email, $voornaam, $verificationCode);

    // ===== STAP 11: LOG DE CODE (BACKUP VOOR DEVELOPMENT) =====
    // Als email niet werkt, kan je de code hier vinden!
    $debugMessage = "=== VERIFICATIECODE ===\n";
    $debugMessage .= "Tijd: " . date('Y-m-d H:i:s') . "\n";
    $debugMessage .= "Email: $email\n";
    $debugMessage .= "Naam: $voornaam $achternaam\n";
    $debugMessage .= "CODE: $verificationCode\n";
    $debugMessage .= "Geldig tot: $expiresAt\n";
    $debugMessage .= "Email verstuurd: " . ($emailSent ? 'JA' : 'NEE (check dit bestand voor de code!)') . "\n";
    $debugMessage .= "======================\n\n";
    file_put_contents(__DIR__ . '/verification_codes.txt', $debugMessage, FILE_APPEND);

    // ===== STAP 12: SLA TIJDELIJKE INFO OP IN SESSIE =====
    // Dit gebruiken we op de verificatie pagina
    $_SESSION['pending_verification'] = [
        'user_id' => $userId,
        'email' => $email,
        'first_name' => $voornaam,
        'last_name' => $achternaam
    ];

    // ===== STAP 13: STUUR SUCCESS RESPONSE =====
    echo json_encode([
        'success' => true,
        'message' => 'Account aangemaakt! Controleer je email voor de verificatiecode.',
        'redirect' => 'verify_email.php'
    ]);
} catch (PDOException $e) {
    // Als er een database fout is, log deze
    error_log("Registratie fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
