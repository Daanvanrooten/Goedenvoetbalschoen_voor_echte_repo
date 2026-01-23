<?php
// Activeer alle gebruikers (sla email verificatie over)
require_once 'db_connection.php';

try {
    $conn = getDbConnection();
    
    // Update alle gebruikers
    $stmt = $conn->query("UPDATE users SET is_email_verified = 1");
    $count = $stmt->rowCount();
    
    // Verwijder alle openstaande verificaties
    $conn->query("DELETE FROM email_verifications");
    
    echo "<h1>✓ Alle gebruikers geactiveerd!</h1>";
    echo "<p><strong>{$count}</strong> gebruikers zijn nu geverifieerd en kunnen inloggen.</p>";
    echo "<p><a href='test_login.php'>Terug naar overzicht</a></p>";
    echo "<p><a href='../views/login.php' style='background: blue; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ga naar Login pagina</a></p>";
    
} catch (PDOException $e) {
    echo "<h1>✗ Fout</h1>";
    echo "<p style='color: red;'>Database fout: " . $e->getMessage() . "</p>";
}
?>
