<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}

// Check of er een pending verification is
if (!isset($_SESSION['pending_verification'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geen verificatie in behandeling']);
    exit;
}

$userId = $_SESSION['pending_verification']['user_id'];
$email = $_SESSION['pending_verification']['email'];
$firstName = $_SESSION['pending_verification']['first_name'];
$lastName = $_SESSION['pending_verification']['last_name'];

try {
    $conn = getDbConnection();

    // Verwijder oude verificatiecodes voor deze gebruiker
    $stmt = $conn->prepare("DELETE FROM email_verifications WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Genereer nieuwe 6-cijferige verificatiecode
    $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Sla nieuwe verificatiecode op
    $stmt = $conn->prepare("
        INSERT INTO email_verifications (user_id, token, created_at, expires_at) 
        VALUES (?, ?, NOW(), ?)
    ");
    $stmt->execute([$userId, $verificationCode, $expiresAt]);

    // Verstuur email
    require_once __DIR__ . '/email_sender.php';
    $emailSent = sendVerificationEmail($email, $firstName, $verificationCode);

    // Log ALTIJD de code (backup)
    $debugMessage = "=== NIEUWE CODE (RESEND) ===\n";
    $debugMessage .= "Tijd: " . date('Y-m-d H:i:s') . "\n";
    $debugMessage .= "Email: $email\n";
    $debugMessage .= "CODE: $verificationCode\n";
    $debugMessage .= "Email verstuurd: " . ($emailSent ? 'JA' : 'NEE (check dit bestand!)') . "\n";
    $debugMessage .= "============================\n\n";
    file_put_contents(__DIR__ . '/verification_codes.txt', $debugMessage, FILE_APPEND);

    echo json_encode([
        'success' => true,
        'message' => 'Nieuwe verificatiecode verzonden naar je email!'
    ]);
} catch (PDOException $e) {
    error_log("Resend verificatie fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
