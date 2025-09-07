<?php
header('Content-Type: application/json');

require_once '../includes/config.php';
require_once '../includes/database.php';

$db = new Database();
$conn = $db->getConnection();

// Get wallet ID from request
$walletId = isset($_GET['wallet_id']) ? $_GET['wallet_id'] : '';

if (empty($walletId)) {
    echo json_encode(['error' => 'Wallet ID is required']);
    exit;
}

// Get wallet address
$query = "SELECT address FROM wallets WHERE id = :wallet_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':wallet_id', $walletId);
$stmt->execute();
$wallet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$wallet) {
    echo json_encode(['error' => 'Wallet not found']);
    exit;
}

// Get wallet activities
$query = "SELECT * FROM activities WHERE wallet_id = :wallet_id ORDER BY created_at DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->bindParam(':wallet_id', $walletId);
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'wallet' => $wallet,
    'activities' => $activities
]);
?>