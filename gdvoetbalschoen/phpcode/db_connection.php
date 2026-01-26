<?php
// Primaire database configuratie (lokaal)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'goudenvoetbalschoen_database');
define('DB_USER', 'root');
define('DB_PASS', '');

// Secundaire database configuratie (fallback) - vul zelf in
define('DB_HOST_FALLBACK', 'localhost'); // Bijv: 'production-server.com' of '192.168.1.100'
define('DB_NAME_FALLBACK', 'klas4s24_597912'); // Bijv: 'goudenvoetbalschoen_database'
define('DB_USER_FALLBACK', 'klas4s24_597912'); // Bijv: 'username'
define('DB_PASS_FALLBACK', '41vfj6GJ'); // Bijv: 'password'

// Maak database connectie met fallback
function getDbConnection()
{
    // Probeer eerst de primaire database connectie
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
        error_log("Verbonden met primaire database");
        return $conn;
    } catch (PDOException $e) {
        error_log("Primaire database connectie mislukt: " . $e->getMessage());

        // Als primaire connectie mislukt, probeer fallback database
        if (!empty(DB_HOST_FALLBACK) && !empty(DB_NAME_FALLBACK)) {
            try {
                $conn = new PDO(
                    "mysql:host=" . DB_HOST_FALLBACK . ";dbname=" . DB_NAME_FALLBACK . ";charset=utf8mb4",
                    DB_USER_FALLBACK,
                    DB_PASS_FALLBACK,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                error_log("Verbonden met fallback database");
                return $conn;
            } catch (PDOException $e2) {
                error_log("Fallback database connectie ook mislukt: " . $e2->getMessage());
                die("Database connectie mislukt. Zowel primaire als fallback database zijn niet bereikbaar.");
            }
        } else {
            die("Database connectie mislukt. Geen fallback database geconfigureerd.");
        }
    }
}
