<?php
// Test script om login probleem te debuggen
require_once 'db_connection.php';

echo "<h1>Login Debug Test</h1>";

try {
    $conn = getDbConnection();
    echo "<p style='color: green;'>✓ Database connectie OK</p>";

    // Haal alle gebruikers op
    $stmt = $conn->query("SELECT user_id, username, email, is_active, is_email_verified, role_id FROM users");
    $users = $stmt->fetchAll();

    echo "<h2>Gebruikers in database:</h2>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Username</th><th>Email</th><th>Actief</th><th>Email Verified</th><th>Role</th><th>Actie</th>";
    echo "</tr>";

    foreach ($users as $user) {
        $activeStatus = $user['is_active'] ? '✓' : '✗';
        $verifiedStatus = $user['is_email_verified'] ? '✓' : '✗';
        $verifiedColor = $user['is_email_verified'] ? 'green' : 'red';

        echo "<tr>";
        echo "<td>{$user['user_id']}</td>";
        echo "<td><strong>{$user['username']}</strong></td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$activeStatus}</td>";
        echo "<td style='color: {$verifiedColor};'><strong>{$verifiedStatus}</strong></td>";
        echo "<td>{$user['role_id']}</td>";
        echo "<td>";
        if (!$user['is_email_verified']) {
            echo "<a href='fix_user.php?user_id={$user['user_id']}' style='color: blue;'>Activeer</a>";
        }
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Check email_verifications tabel
    $stmt = $conn->query("SELECT COUNT(*) as count FROM email_verifications");
    $result = $stmt->fetch();
    echo "<h2>Email Verificaties:</h2>";
    echo "<p>Aantal openstaande verificaties: <strong>{$result['count']}</strong></p>";

    if ($result['count'] > 0) {
        $stmt = $conn->query("SELECT ev.*, u.username, u.email FROM email_verifications ev JOIN users u ON ev.user_id = u.user_id");
        $verifications = $stmt->fetchAll();

        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>User ID</th><th>Username</th><th>Email</th><th>Code</th><th>Expires</th>";
        echo "</tr>";

        foreach ($verifications as $ver) {
            $expired = strtotime($ver['expires_at']) < time() ? ' style="color: red;"' : '';
            echo "<tr{$expired}>";
            echo "<td>{$ver['user_id']}</td>";
            echo "<td>{$ver['username']}</td>";
            echo "<td>{$ver['email']}</td>";
            echo "<td><strong>{$ver['token']}</strong></td>";
            echo "<td>{$ver['expires_at']}</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database fout: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Quick Fix Opties:</h2>";
echo "<p><a href='fix_all_users.php' style='background: green; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Activeer ALLE gebruikers (sla verificatie over)</a></p>";
echo "<p style='color: #666; font-size: 12px;'>Dit zet is_email_verified = 1 voor alle gebruikers</p>";
