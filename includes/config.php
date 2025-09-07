<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'solana_monitor');
define('DB_USER', 'root');
define('DB_PASS', 'HJghJjh3JfWVUjc');

// Default admin credentials
define('DEFAULT_USERNAME', 'admin');
define('DEFAULT_PASSWORD', 'admin123'); // Change this after installation

// Telegram configuration
define('TELEGRAM_TOKEN', '000000:AAEYhKW300000000000');
define('TELEGRAM_CHAT_ID', '-0000000');

// Tor configuration
define('TOR_PASSWORD', "16:DBEA9251E1B8DD9A600000FF3EFB2CF715C99D7E9D64AD3E846FB0E");
define('TOR_CONTROL_PORT', "9050");

// API headers
$headers = [
    ':authority: api-v2.solscan.io',
    ':method: GET',
    ':scheme: https',
    'accept: application/json, text/plain, */*',
    'accept-encoding: gzip, deflate, br, zstd',
    'accept-language: en-US,en;q=0.9,fa-IR;q=0.8,fa;q=0.7,zh-CN;q=0.6,zh;q=0.5,sq;q=0.4',
    'cookie: _ga=GA1.1.1135464142.1737542313; cf_clearance=Wlu7y33a8uH1MyQijYSrc.hLm2LI0bHgHP.ZM4PtfGM-1743856709-1.2.1.1-6MihH_ZIYzdxWDTu8sCYFClSkIQ0B1Aau6c5glWlL1UwB5nfdsWUGXN3kLBEQKSWBaF4rACtajUNPAzeGizwp0vOpKvm_3x4LNIL0ZMak2fx8KI3WZX5wJRRWdpq_MFCHHklP8UNZ8TPKvFm3E5Xxi.VmSEg6sWij8d3rOf8paxYAjFJ3cTx2Yqbrozt8MyY7AcioSzov4f8nUVmedErQr3nOnboXLvSn5rxpWIYQ62u8_9t6xEXyMrRebE6vMDFmNKMsGp6y9cmdED5W7hgh66FSMvF44yffj0rW5khX3q7hAK00YBU9Y00svrJ9dghTD0BKO6LGAsu3mobQ_mdO6hqY7_eJaKpOFGsd1_g92g; _ga_PS3V7V7KV0=GS1.1.1745033379.82.0.1745033379.0.0.0',
    'origin: https://solscan.io',
    'priority: u=1, i',
    'referer: https://solscan.io/',
    'sec-ch-ua: "Google Chrome";v="135", "Not-A.Brand";v="8", "Chromium";v="135"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-site',
    'token: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImNvbW1lcmNpYWwuaXJhbjEyM0BnbWFpbC5jb20iLCJhY3Rpb24iOiJsb2dnZWQiLCJpYXQiOjE3Mzc5Mjg4NzAsImV4cCI6MTc0ODcyODg3MH0.UeV9u_A69rVYhH02ZWai2g19RFp1OoLdfJ4O0qHn7Is',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36'
];

define('API_HEADERS', serialize($headers));
?>