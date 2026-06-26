<?php
require_once 'config.php';
requireAuth();

$table = $_GET['table'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$tab = $_GET['tab'] ?? 'bookings';
$allowedTables = ['bookings', 'contacts'];
$allowedStatuses = ['replied', 'completed'];

if (in_array($table, $allowedTables) && $id && in_array($status, $allowedStatuses)) {
    $stmt = $pdo->prepare("UPDATE $table SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

header("Location: index.php?tab=$tab&page=$page");
exit;
