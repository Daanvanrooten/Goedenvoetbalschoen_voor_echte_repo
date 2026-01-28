<?php
session_start();
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

// Alleen admins mogen dit uitvoeren
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Geen toegang']);
    exit;
}

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$new_role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 1;

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geen geldige gebruiker geselecteerd']);
    exit;
}

try {
    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE users SET role_id = ? WHERE user_id = ?");
    $stmt->execute([$new_role_id, $user_id]);
    echo json_encode(['success' => true, 'message' => 'Rol succesvol aangepast']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
