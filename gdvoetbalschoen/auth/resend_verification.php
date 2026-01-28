<?php

/**
 * RESEND_VERIFICATION.PHP - Verstuurt een nieuwe verificatiecode
 * 
 * Dit script wordt gebruikt als de gebruiker:
 * - De eerste code niet heeft ontvangen
 * - De code is verlopen (na 15 minuten)
 * - Een nieuwe code wil aanvragen
 * 
 * Het genereert een nieuwe 6-cijferige code en verstuurt deze
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
// Dit wordt opgeslagen tijdens registratie
if (!isset($_SESSION['pending_verification'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geen verificatie in behandeling']);
    exit;
}

// Haal de tijdelijke info op uit de sessie
$userId = $_SESSION['pending_verification']['user_id'];
$email = $_SESSION['pending_verification']['email'];
$firstName = $_SESSION['pending_verification']['first_name'];
$lastName = $_SESSION['pending_verification']['last_name'];

try {
    // ===== STAP 2: MAAK DATABASE VERBINDING =====
    $conn = getDbConnection();

    // ===== STAP 3: VERWIJDER OUDE VERIFICATIECODES =====
    // We willen alleen de nieuwste code geldig hebben
    $stmt = $conn->prepare("DELETE FROM email_verifications WHERE user_id = ?");
    $stmt->execute([$userId]);

    // ===== STAP 4: GENEREER NIEUWE VERIFICATIECODE =====
    // Nieuwe random 6-cijferige code
    $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Weer 15 minuten geldig vanaf nu
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // ===== STAP 5: SLA NIEUWE CODE OP IN DATABASE =====
    $stmt = $conn->prepare("
        INSERT INTO email_verifications (user_id, token, created_at, expires_at) 
        VALUES (?, ?, NOW(), ?)
    ");
    $stmt->execute([$userId, $verificationCode, $expiresAt]);

    // ===== STAP 6: VERSTUUR EMAIL MET NIEUWE CODE =====
    require_once __DIR__ . '/email_sender.php';
    $emailSent = sendVerificationEmail($email, $firstName, $verificationCode);

    // ===== STAP 7: LOG DE NIEUWE CODE (BACKUP) =====
    $debugMessage = "=== NIEUWE CODE (RESEND) ===\n";
    $debugMessage .= "Tijd: " . date('Y-m-d H:i:s') . "\n";
    $debugMessage .= "Email: $email\n";
    $debugMessage .= "CODE: $verificationCode\n";
    $debugMessage .= "Email verstuurd: " . ($emailSent ? 'JA' : 'NEE (check dit bestand!)') . "\n";
    $debugMessage .= "============================\n\n";
    file_put_contents(__DIR__ . '/verification_codes.txt', $debugMessage, FILE_APPEND);

    // ===== STAP 8: STUUR SUCCESS RESPONSE =====
    echo json_encode([
        'success' => true,
        'message' => 'Nieuwe verificatiecode verzonden naar je email!'
    ]);
} catch (PDOException $e) {
    // Als er een database fout is, log deze
    error_log("Resend verificatie fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
