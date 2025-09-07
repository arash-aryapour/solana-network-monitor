<?php

function getReportedActivities($wallet) {
    $file = "reported_activities_$wallet.json";
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true) ?: [];
    }
    return [];
}

function saveReportedActivity($wallet, $activity) {
    $file = "reported_activities_$wallet.json";
    $activities = getReportedActivities($wallet);
    

    $activities = array_slice($activities, -99);
    $activities[] = $activity;
    
    file_put_contents($file, json_encode($activities));
}


function createActivityHash($activity) {

    return md5($activity['block_id'] . '-' . $activity['trans_id']);
}


function changeTorIP() {
    $torPassword = TOR_PASSWORD;
    $torControlPort = TOR_CONTROL_PORT;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:$torControlPort");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "AUTHENTICATE \"$torPassword\"\r\nSIGNAL NEWNYM\r\n");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response !== false;
}


function sendRequestThroughTor($url, $headers) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PROXY, "localhost:9050");
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}


function getCurrentIP() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.ipify.org");
    curl_setopt($ch, CURLOPT_PROXY, "localhost:9050");
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $ip = curl_exec($ch);
    curl_close($ch);
    return $ip;
}
?>