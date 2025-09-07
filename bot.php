<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/telegram_api.php';

// Initialize database connection
$db = new Database();
$conn = $db->getConnection(); // این خط را اضافه کنید

// Get settings
$query = "SELECT * FROM settings LIMIT 1";
$stmt = $conn->prepare($query); // از $conn استفاده کنید
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all wallets
$query = "SELECT * FROM wallets";
$stmt = $conn->prepare($query); // از $conn استفاده کنید
$stmt->execute();
$wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Change IP if Tor is enabled and interval has passed
if ($settings['tor_enabled'] && (time() % $settings['check_interval'] == 0)) {
    changeTorIP();
}

// API headers
$headers = unserialize(API_HEADERS);

foreach ($wallets as $wallet) {
    // Update API path with current wallet
    $headers[':path'] = "/v2/account/activity/dextrading?address={$wallet['address']}&page=1&page_size=10";
    
    $response = sendRequestThroughTor("https://api-v2.solscan.io/v2/account/activity/dextrading?address={$wallet['address']}&page=1&page_size=10", $headers);
    
    echo "Checking wallet: {$wallet['address']}\n";
    echo "API Response: " . $response . "\n";
    
    if ($response === false) {
        $errorMessage = "⚠️ Connection to API failed for wallet: {$wallet['address']} ⚠️";
        if ($settings['telegram_enabled']) {
            sendTelegramMessage($errorMessage, $settings['telegram_token'], $settings['telegram_chat_id']);
        }
        continue;
    }
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success'] && !empty($data['data'])) {
            $firstActivity = $data['data'][0];
            $activityHash = createActivityHash($firstActivity);
            
            // Check if this activity has been reported before
            $query = "SELECT id FROM activities WHERE wallet_id = :wallet_id AND activity_hash = :activity_hash";
            $stmt = $conn->prepare($query); // از $conn استفاده کنید
            $stmt->bindParam(':wallet_id', $wallet['id']);
            $stmt->bindParam(':activity_hash', $activityHash);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                // This is a new activity
                $tokenName = isset($firstActivity['amount_info']['token2']) ? 
                    $firstActivity['amount_info']['token2'] : 
                    (isset($firstActivity['amount_info']['token1']) ? 
                    $firstActivity['amount_info']['token1'] : 'Unknown Token');
                
                // Prepare message based on activity type
                $message = '';
                switch ($firstActivity['activity_type']) {
                    case 'ACTIVITY_TOKEN_ADD_LIQ':
                        $message = "⚠️✳️ ADD LIQUIDITY ✳️⚠️\n";
                        $message .= "📍 Token: " . $tokenName . "\n";
                        $message .= "💰 Value: $" . number_format($firstActivity['value'], 2) . "\n";
                        $message .= "📊 Block: " . $firstActivity['block_id'] . "\n";
                        $message .= "🆔 TX: " . substr($firstActivity['trans_id'], 0, 10) . "...\n";
                        break;
                    case 'ACTIVITY_TOKEN_REMOVE_LIQ':
                        $message = "♨️ REMOVE LIQUIDITY 🕸\n";
                        $message .= "📍 Token: " . $tokenName . "\n";
                        $message .= "💰 Value: $" . number_format($firstActivity['value'], 2) . "\n";
                        $message .= "📊 Block: " . $firstActivity['block_id'] . "\n";
                        $message .= "🆔 TX: " . substr($firstActivity['trans_id'], 0, 10) . "...\n";
                        break;
                    case 'ACTIVITY_SPL_INIT_MINT':
                        $message = "✅ CREATE TOKEN 🛂\n";
                        $message .= "📍 Token: " . $tokenName . "\n";
                        $message .= "📊 Block: " . $firstActivity['block_id'] . "\n";
                        $message .= "🆔 TX: " . substr($firstActivity['trans_id'], 0, 10) . "...\n";
                        break;
                    case 'ACTIVITY_AGG_TOKEN_SWAP':
                        $message = "🔄 TOKEN SWAP 🔄\n";
                        $message .= "📍 From: " . $firstActivity['amount_info']['token1'] . "\n";
                        $message .= "📍 To: " . $firstActivity['amount_info']['token2'] . "\n";
                        $message .= "💰 Value: $" . number_format($firstActivity['value'], 2) . "\n";
                        $message .= "📊 Block: " . $firstActivity['block_id'] . "\n";
                        $message .= "🆔 TX: " . substr($firstActivity['trans_id'], 0, 10) . "...\n";
                        break;
                    default:
                        $message = "Activity Type: " . $firstActivity['activity_type'] . "\n";
                        $message .= "📊 Block: " . $firstActivity['block_id'] . "\n";
                        $message .= "🆔 TX: " . substr($firstActivity['trans_id'], 0, 10) . "...\n";
                        break;
                }
                
                // Add separator and wallet address
                $message .= "▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️\n";
                $message .= "◾️ [Wallet: {$wallet['address']}]\n";
                
                // Save activity to database
                $query = "INSERT INTO activities (wallet_id, activity_type, token_name, value, block_id, trans_id, activity_hash, created_at) 
                          VALUES (:wallet_id, :activity_type, :token_name, :value, :block_id, :trans_id, :activity_hash, NOW())";
                $stmt = $conn->prepare($query); // از $conn استفاده کنید
                $stmt->bindParam(':wallet_id', $wallet['id']);
                $stmt->bindParam(':activity_type', $firstActivity['activity_type']);
                $stmt->bindParam(':token_name', $tokenName);
                $stmt->bindParam(':value', $firstActivity['value']);
                $stmt->bindParam(':block_id', $firstActivity['block_id']);
                $stmt->bindParam(':trans_id', $firstActivity['trans_id']);
                $stmt->bindParam(':activity_hash', $activityHash);
                $stmt->execute();
                
                // Send Telegram notification if enabled
                if ($settings['telegram_enabled']) {
                    $solscanUrl = "https://solscan.io/account/{$wallet['address']}";
                    $keyboard = [
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => '🔍 View on Solscan',
                                    'url' => $solscanUrl
                                ]
                            ]
                        ]
                    ];
                    
                    sendTelegramMessage($message, $settings['telegram_token'], $settings['telegram_chat_id'], $keyboard);
                }
                
                echo "New activity detected and saved to database.\n";
            } else {
                echo "Activity already reported, skipping.\n";
            }
        } else {
            echo "No new activity found for wallet: {$wallet['address']}\n";
        }
    } else {
        $message = "🔴 Failed to get response from API for wallet: {$wallet['address']}";
        if ($settings['telegram_enabled']) {
            sendTelegramMessage($message, $settings['telegram_token'], $settings['telegram_chat_id']);
        }
    }
    
    // Delay between checking different wallets
    sleep(1);
}

// Display current IP
echo "Current IP: " . getCurrentIP() . "\n";
?>