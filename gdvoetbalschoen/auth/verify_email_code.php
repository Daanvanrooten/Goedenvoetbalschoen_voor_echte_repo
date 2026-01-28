<?php
session_start();
require_once '../config/db_connection.php';

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
$code = trim($_POST['verification_code'] ?? '');

// Validatie
if (empty($code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Verificatiecode is verplicht']);
    exit;
}

if (!preg_match('/^[0-9]{6}$/', $code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ongeldige code format. Code moet 6 cijfers zijn']);
    exit;
}

try {
    $conn = getDbConnection();

    // Haal verificatie record op
    $stmt = $conn->prepare("
        SELECT token_id, token, expires_at 
        FROM email_verifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $verification = $stmt->fetch();

    // Check of verificatie bestaat
    if (!$verification) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Geen verificatiecode gevonden. Vraag een nieuwe aan.']);
        exit;
    }

    // Check of code verlopen is
    if (strtotime($verification['expires_at']) < time()) {
        // Verwijder verlopen code
        $stmt = $conn->prepare("DELETE FROM email_verifications WHERE token_id = ?");
        $stmt->execute([$verification['token_id']]);

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Verificatiecode is verlopen. Vraag een nieuwe aan.']);
        exit;
    }

    // Verifieer code
    if ($verification['token'] !== $code) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Onjuiste verificatiecode. Probeer opnieuw.']);
        exit;
    }

    // Code is correct! Update gebruiker
    $stmt = $conn->prepare("UPDATE users SET is_email_verified = 1, updated_at = NOW() WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Verwijder verificatie record
    $stmt = $conn->prepare("DELETE FROM email_verifications WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Haal volledige gebruikersgegevens op
    $stmt = $conn->prepare("
        SELECT user_id, first_name, last_name, email, telefoonnummer, username, role_id 
        FROM users 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    // Log gebruiker in
    $_SESSION['user'] = [
        'id' => $user['user_id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'telefoonnummer' => $user['telefoonnummer'],
        'username' => $user['username'],
        'role_id' => $user['role_id']
    ];

    // Verwijder pending verification
    unset($_SESSION['pending_verification']);

    echo json_encode([
        'success' => true,
        'message' => 'Email succesvol geverifieerd! Je account is nu actief.',
        'redirect' => 'agenda.php'
    ]);
} catch (PDOException $e) {
    error_log("Verificatie fout: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database fout: ' . $e->getMessage()
    ]);
}
