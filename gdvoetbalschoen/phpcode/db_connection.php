<?php
// lokale database
$db_host = '127.0.0.1';
$db_name = 'goudenvoetbalschoen_database';
$db_user = 'root';
$db_pass = '';

// server database (backup)
$db_host2 = 'localhost';
$db_name2 = 'klas4s24_597912';
$db_user2 = 'klas4s24_597912';
$db_pass2 = '41vfj6GJ';

function getDbConnection()
{
    global $db_host, $db_name, $db_user, $db_pass;
    global $db_host2, $db_name2, $db_user2, $db_pass2;

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // probeer server database
        try {
            $pdo = new PDO("mysql:host=$db_host2;dbname=$db_name2", $db_user2, $db_pass2);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Connectie mislukt");
        }
    }
}
