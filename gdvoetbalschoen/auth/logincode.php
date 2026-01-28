<?php

/**
 * LOGINCODE.PHP - Verwerkt login verzoeken van gebruikers
 * 
 * Dit script checkt of:
 * 1. De gebruikersnaam bestaat in de database
 * 2. Het wachtwoord correct is
 * 3. Het account actief is
 * 4. De email is geverifieerd
 * 
 * Als alles klopt wordt de gebruiker ingelogd en doorgestuurd
 */

// Start de sessie - nodig om gebruiker ingelogd te houden
session_start();

// Maak connectie met database
require_once '../config/db_connection.php';

// Zeg dat we JSON terug sturen (geen HTML)
header('Content-Type: application/json');

// Check of dit een POST request is (formulier versturen)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}

// Haal login data op
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

    // Zoek gebruiker in database
    $stmt = $conn->prepare("
        SELECT user_id, role_id, first_name, last_name, email, username, password_hash, is_active, is_email_verified 
        FROM users 
        WHERE username = ?
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Check 1: bestaat gebruiker?
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Gebruikersnaam of wachtwoord is onjuist']);
        exit;
    }

    // Check 2: is account actief?
    if (!$user['is_active']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Account is gedeactiveerd. Neem contact op met de beheerder.']);
        exit;
    }

    // Check 3: is email geverifieerd?
    if (!$user['is_email_verified']) {
        // Sla info op en stuur naar verificatie pagina
        $_SESSION['pending_verification'] = [
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name']
        ];
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Email nog niet geverifieerd. Je wordt doorgestuurd naar de verificatiepagina.',
            'redirect' => 'verify_email.php'
        ]);
        exit;
    }

    // Check 4: wachtwoord correct?
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Gebruikersnaam of wachtwoord is onjuist']);
        exit;
    }

    // ===== ALLES KLOPT! LOG GEBRUIKER IN =====
    // Sla gebruikersgegevens op in sessie (blijft bewaard tijdens surfen)
    $_SESSION['user'] = [
        'id' => $user['user_id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'username' => $user['username'],
        'role_id' => $user['role_id']
    ];

    // DEBUG: Log sessie naar bestand (handig voor troubleshooting)
    file_put_contents(__DIR__ . '/debug_session.txt', print_r($_SESSION['user'], true));

    // Bepaal waar de gebruiker naartoe moet
    // role_id 2 = admin -> admin dashboard
    // anders -> agenda pagina
    $redirect = ($user['role_id'] == 2) ? '../views/admin_dashboard.php' : 'agenda.php';

    // Stuur success response terug
    echo json_encode([
        'success' => true,
        'message' => 'Login succesvol!',
        'redirect' => $redirect
    ]);
} catch (PDOException $e) {
    // Als er een database fout is, log deze en stuur error terug
    error_log("Login fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
