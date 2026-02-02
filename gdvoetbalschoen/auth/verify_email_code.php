<?php

/**
 * VERIFY_EMAIL_CODE.PHP - Controleert de verificatiecode van de gebruiker
 * 
 * Dit script:
 * 1. Checkt of de ingevoerde code correct is
 * 2. Checkt of de code niet verlopen is (max 15 minuten geldig)
 * 3. Activeert het account als alles klopt
 * 4. Logt de gebruiker automatisch in
 * 5. Stuurt door naar de agenda pagina
 */

// Start sessie om gebruikersinfo op te halen
session_start();

// Maak database verbinding
require_once '../config/db_connection.php';

// Zeg dat we JSON terug sturen
header('Content-Type: application/json');

// Check of dit een POST request is
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}

// ===== STAP 1: CHECK OF ER EEN VERIFICATIE IN BEHANDELING IS =====
if (!isset($_SESSION['pending_verification'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geen verificatie in behandeling']);
    exit;
}

// Haal gebruiker ID op uit sessie
$userId = $_SESSION['pending_verification']['user_id'];

// Haal de ingevoerde code op uit het formulier
$code = trim($_POST['verification_code'] ?? '');

// ===== STAP 2: VALIDATIE VAN DE CODE =====
if (empty($code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Verificatiecode is verplicht']);
    exit;
}

// Check of het format klopt (moet precies 6 cijfers zijn)
if (!preg_match('/^[0-9]{6}$/', $code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ongeldige code format. Code moet 6 cijfers zijn']);
    exit;
}

try {
    // ===== STAP 3: MAAK DATABASE VERBINDING =====
    $conn = getDbConnection();

    // ===== STAP 4: HAAL DE VERIFICATIECODE OP UIT DATABASE =====
    // We pakken de nieuwste code (ORDER BY created_at DESC)
    $stmt = $conn->prepare("
        SELECT token_id, token, expires_at 
        FROM email_verifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $verification = $stmt->fetch();

    // ===== CHECK 1: BESTAAT ER EEN CODE? =====
    if (!$verification) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Geen verificatiecode gevonden. Vraag een nieuwe aan.']);
        exit;
    }

    // ===== CHECK 2: IS DE CODE VERLOPEN? =====
    // Codes zijn maar 15 minuten geldig
    if (strtotime($verification['expires_at']) < time()) {
        // Code is verlopen, verwijder deze uit database
        $stmt = $conn->prepare("DELETE FROM email_verifications WHERE token_id = ?");
        $stmt->execute([$verification['token_id']]);

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Verificatiecode is verlopen. Vraag een nieuwe aan.']);
        exit;
    }

    // ===== CHECK 3: IS DE CODE CORRECT? =====
    // Vergelijk ingevoerde code met code uit database
    if ($verification['token'] !== $code) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Onjuiste verificatiecode. Probeer opnieuw.']);
        exit;
    }

    // ===== ALLE CHECKS GELUKT! ACTIVEER HET ACCOUNT =====

    // STAP 5: Zet is_email_verified op 1 (TRUE) in database
    $stmt = $conn->prepare("UPDATE users SET is_email_verified = 1, updated_at = NOW() WHERE user_id = ?");
    $stmt->execute([$userId]);

    // STAP 6: Verwijder de gebruikte verificatiecode
    $stmt = $conn->prepare("DELETE FROM email_verifications WHERE user_id = ?");
    $stmt->execute([$userId]);

    // ===== STAP 7: HAAL VOLLEDIGE GEBRUIKERSGEGEVENS OP =====
    $stmt = $conn->prepare("
        SELECT user_id, first_name, last_name, email, telefoonnummer, username, role_id 
        FROM users 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    // ===== STAP 8: LOG GEBRUIKER AUTOMATISCH IN =====
    // Sla alle info op in sessie zodat gebruiker ingelogd blijft
    $_SESSION['user'] = [
        'id' => $user['user_id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'telefoonnummer' => $user['telefoonnummer'],
        'username' => $user['username'],
        'role_id' => $user['role_id']
    ];

    // ===== STAP 9: RUIM TIJDELIJKE DATA OP =====
    // pending_verification hoeven we niet meer, verificatie is klaar!
    unset($_SESSION['pending_verification']);

    // ===== STAP 10: STUUR SUCCESS RESPONSE =====
    echo json_encode([
        'success' => true,
        'message' => 'Email succesvol geverifieerd! Je account is nu actief.',
        'redirect' => 'agenda.php'  // Stuur gebruiker naar hoofdpagina
    ]);
} catch (PDOException $e) {
    // Als er een database fout is, log deze
    error_log("Verificatie fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
