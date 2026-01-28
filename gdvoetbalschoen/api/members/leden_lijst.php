<?php
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

try {
    $conn = getDbConnection();
    $stmt = $conn->query("SELECT user_id, first_name, last_name, email, telefoonnummer, username, role_id FROM users");
    $leden = $stmt->fetchAll();
    $aantal = count($leden);
    echo json_encode(['success' => true, 'leden' => $leden, 'aantal' => $aantal]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
