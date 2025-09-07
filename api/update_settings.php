<?php
header('Content-Type: application/json');

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$db = new Database();
$conn = $db->getConnection(); 

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

$telegram_enabled = isset($data['telegram_enabled']) ? 1 : 0;
$telegram_token = $data['telegram_token'] ?? '';
$telegram_chat_id = $data['telegram_chat_id'] ?? '';
$tor_enabled = isset($data['tor_enabled']) ? 1 : 0;
$check_interval = $data['check_interval'] ?? 60;

$query = "UPDATE settings SET 
          telegram_enabled = :telegram_enabled,
          telegram_token = :telegram_token,
          telegram_chat_id = :telegram_chat_id,
          tor_enabled = :tor_enabled,
          check_interval = :check_interval
          WHERE id = 1";

$stmt = $conn->prepare($query); // از $conn استفاده کنید
$stmt->bindParam(':telegram_enabled', $telegram_enabled);
$stmt->bindParam(':telegram_token', $telegram_token);
$stmt->bindParam(':telegram_chat_id', $telegram_chat_id);
$stmt->bindParam(':tor_enabled', $tor_enabled);
$stmt->bindParam(':check_interval', $check_interval);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update settings']);
}
?>