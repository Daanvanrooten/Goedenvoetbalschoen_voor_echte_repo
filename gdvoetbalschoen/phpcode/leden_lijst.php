<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

try {
    $conn = getDbConnection();
    $stmt = $conn->query("SELECT user_id, first_name, last_name, email, username, role_id FROM users");
    $leden = $stmt->fetchAll();
    echo json_encode(['success' => true, 'leden' => $leden]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
