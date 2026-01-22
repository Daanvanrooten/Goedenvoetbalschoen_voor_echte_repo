<?php
// Database configuratie
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'goudenvoetbalschoen_database');
define('DB_USER', 'root');
define('DB_PASS', '');

// Maak database connectie
function getDbConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $conn;
    } catch (PDOException $e) {
        error_log("Database connectie fout: " . $e->getMessage());
        die("Database connectie mislukt. Probeer het later opnieuw.");
    }
}
?>
