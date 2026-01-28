<?php
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = isset($_GET['perPage']) ? max(1, (int)$_GET['perPage']) : 10;
$offset = ($page - 1) * $perPage;

try {
    $conn = getDbConnection();
    $totalStmt = $conn->query("SELECT COUNT(*) FROM users");
    $totalLeden = (int)$totalStmt->fetchColumn();
    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, telefoonnummer, username, role_id FROM users ORDER BY user_id LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $leden = $stmt->fetchAll();
    echo json_encode([
        'success' => true,
        'leden' => $leden,
        'aantal' => $totalLeden,
        'perPage' => $perPage,
        'page' => $page,
        'totalPages' => ceil($totalLeden / $perPage)
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
