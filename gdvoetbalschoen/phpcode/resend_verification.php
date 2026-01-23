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

    // Verstuur email met verificatiecode
    require_once __DIR__ . '/email_functions.php';
    $emailSent = sendVerificationEmail($email, "$firstName $lastName", $verificationCode);
    
    // Log voor debug
    $debugMessage = "=== EMAIL VERIFICATIE (RESEND) ===\n";
    $debugMessage .= "Tijd: " . date('Y-m-d H:i:s') . "\n";
    $debugMessage .= "Naar: $email\n";
    $debugMessage .= "Gebruiker: $firstName $lastName\n";
    $debugMessage .= "Verificatiecode: $verificationCode\n";
    $debugMessage .= "Geldig tot: $expiresAt\n";
    $debugMessage .= "Email verzonden: " . ($emailSent ? 'JA' : 'NEE') . "\n";
    ]);
} catch (PDOException $e) {
    error_log("Resend verificatie fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
