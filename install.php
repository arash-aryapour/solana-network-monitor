<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

// Create database connection
$db = new Database();
$conn = $db->getConnection(); // این خط را اضافه کنید

// Create tables
$query = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->exec($query); // از $conn استفاده کنید

$query = "CREATE TABLE IF NOT EXISTS wallets (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->exec($query); // از $conn استفاده کنید

$query = "CREATE TABLE IF NOT EXISTS activities (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    wallet_id INT(11) NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    token_name VARCHAR(255) NOT NULL,
    value DECIMAL(20, 8) NOT NULL,
    block_id VARCHAR(50) NOT NULL,
    trans_id VARCHAR(100) NOT NULL,
    activity_hash VARCHAR(32) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE
)";
$conn->exec($query); // از $conn استفاده کنید

$query = "CREATE TABLE IF NOT EXISTS settings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    telegram_enabled TINYINT(1) DEFAULT 0,
    telegram_token VARCHAR(255) DEFAULT '',
    telegram_chat_id VARCHAR(50) DEFAULT '',
    tor_enabled TINYINT(1) DEFAULT 0,
    check_interval INT(11) DEFAULT 60
)";
$conn->exec($query); // از $conn استفاده کنید

// Insert default admin user
$username = DEFAULT_USERNAME;
$password = password_hash(DEFAULT_PASSWORD, PASSWORD_DEFAULT);

$query = "INSERT IGNORE INTO users (username, password) VALUES (:username, :password)";
$stmt = $conn->prepare($query); // از $conn استفاده کنید
$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);
$stmt->execute();

// Insert default settings
$query = "INSERT IGNORE INTO settings (id, telegram_enabled, telegram_token, telegram_chat_id, tor_enabled, check_interval) 
          VALUES (1, 1, :telegram_token, :telegram_chat_id, 1, 60)";
$stmt = $conn->prepare($query); // از $conn استفاده کنید
$stmt->bindParam(':telegram_token', TELEGRAM_TOKEN);
$stmt->bindParam(':telegram_chat_id', TELEGRAM_CHAT_ID);
$stmt->execute();

// Insert default wallets
$defaultWallets = [
    ['7bR7vdnTSVLDmPCf6xDnEPvLiWJr6DB7MsnbZTp6srwR', 'Wallet 1'],
    ['9eyU8obGpxRY4ReUoYgpPF9TjvHsirb89KDMshG8urmh', 'Wallet 2'],
    ['GeiCif4ntB7ebVkezfED4RcC2tjBGQc1SuNbPAjQJQeT', 'Wallet 3'],
    ['Lnab94JFcpdKefetiQyLYqXV1XtC1FPDup2Q4Ypg5Mt', 'Wallet 4']
];

foreach ($defaultWallets as $wallet) {
    $query = "INSERT IGNORE INTO wallets (address, name) VALUES (:address, :name)";
    $stmt = $conn->prepare($query); // از $conn استفاده کنید
    $stmt->bindParam(':address', $wallet[0]);
    $stmt->bindParam(':name', $wallet[1]);
    $stmt->execute();
}

echo "Installation completed successfully!<br>";
echo "Default login credentials:<br>";
echo "Username: " . DEFAULT_USERNAME . "<br>";
echo "Password: " . DEFAULT_PASSWORD . "<br>";
echo "<a href='admin/index.php'>Go to Login Page</a>";
?>