<?php
// Fix individuele gebruiker
require_once 'db_connection.php';

$userId = $_GET['user_id'] ?? 0;

if ($userId <= 0) {
    die("Ongeldige gebruiker ID");
}

try {
    $conn = getDbConnection();

    // Update gebruiker
    $stmt = $conn->prepare("UPDATE users SET is_email_verified = 1 WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Verwijder openstaande verificaties
    $stmt = $conn->prepare("DELETE FROM email_verifications WHERE user_id = ?");
    $stmt->execute([$userId]);

    echo "<h1>✓ Gebruiker geactiveerd!</h1>";
    echo "<p>Gebruiker ID {$userId} is nu geverifieerd en kan inloggen.</p>";
    echo "<p><a href='test_login.php'>Terug naar overzicht</a></p>";
} catch (PDOException $e) {
    echo "<h1>✗ Fout</h1>";
    echo "<p style='color: red;'>Database fout: " . $e->getMessage() . "</p>";
}
