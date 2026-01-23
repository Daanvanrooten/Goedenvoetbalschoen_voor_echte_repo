<?php
echo "<h1>✓ PHP Werkt!</h1>";
echo "<p>Als je dit ziet, werkt PHP en is de URL correct.</p>";
echo "<hr>";
echo "<h2>Server Info:</h2>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Filename:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Server Name:</strong> " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<hr>";

// Bereken relatieve pad naar andere bestanden
$currentDir = __DIR__;
echo "<h2>Huidige Directory:</h2>";
echo "<p>$currentDir</p>";

echo "<hr>";
echo "<h2>Juiste URLs voor jouw setup:</h2>";

// Test of registercode.php bestaat
if (file_exists(__DIR__ . '/registercode.php')) {
    echo "<p style='color: green;'>✓ registercode.php gevonden in deze directory</p>";

    // Bereken de juiste URL
    $scriptPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
    $scriptPath = str_replace('\\', '/', $scriptPath);

    echo "<p><strong>Test registratie URL:</strong><br>";
    echo "<a href='http://{$_SERVER['SERVER_NAME']}{$scriptPath}/test_register.php' style='color: blue; font-size: 18px;'>http://{$_SERVER['SERVER_NAME']}{$scriptPath}/test_register.php</a></p>";

    echo "<p><strong>Register pagina URL:</strong><br>";
    $viewsPath = str_replace('phpcode', 'views', $scriptPath);
    echo "<a href='http://{$_SERVER['SERVER_NAME']}{$viewsPath}/register.php' style='color: blue; font-size: 18px;'>http://{$_SERVER['SERVER_NAME']}{$viewsPath}/register.php</a></p>";
} else {
    echo "<p style='color: red;'>✗ registercode.php NIET gevonden in deze directory</p>";
}

echo "<hr>";
echo "<h2>Test Database Connectie:</h2>";
try {
    require_once 'db_connection.php';
    $conn = getDbConnection();
    echo "<p style='color: green;'>✓ Database connectie OK</p>";

    // Check users tabel
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p>Aantal gebruikers in database: <strong>{$result['count']}</strong></p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database fout: " . $e->getMessage() . "</p>";
}
