<?php

function sendTelegramMessage($message, $token = null, $chatId = null, $keyboard = null) {
    $telegramToken = $token ?: TELEGRAM_TOKEN;
    $chatId = $chatId ?: TELEGRAM_CHAT_ID;
    $telegramUrl = "https://api.telegram.org/bot$telegramToken/sendMessage";
    
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    if ($keyboard) {
        $postData['reply_markup'] = json_encode($keyboard);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegramUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $telegramResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch) . "\n";
    }
    
    curl_close($ch);
    echo "Telegram API HTTP Code: " . $httpCode . "\n";
    echo "Telegram API Response: " . $telegramResponse . "\n";
    return $httpCode == 200;
}
?>