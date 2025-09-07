<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/auth.php';

requireLogin();

$db = new Database();
$conn = $db->getConnection(); // این خط را اضافه کنید

// Get current settings
$query = "SELECT * FROM settings LIMIT 1";
$stmt = $conn->prepare($query); // از $conn استفاده کنید
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telegram_enabled = isset($_POST['telegram_enabled']) ? 1 : 0;
    $telegram_token = $_POST['telegram_token'];
    $telegram_chat_id = $_POST['telegram_chat_id'];
    $tor_enabled = isset($_POST['tor_enabled']) ? 1 : 0;
    $check_interval = $_POST['check_interval'];
    
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
        $success = "Settings updated successfully!";
    } else {
        $error = "Failed to update settings!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Solana Analytics Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Solana Analytics Dashboard</a>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Welcome, <?php echo $_SESSION['username']; ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-5 pt-3">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="wallets.php">
                                <i class="fas fa-wallet"></i> Wallets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Settings</h1>
                </div>

                <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">System Configuration</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="telegram_enabled" name="telegram_enabled" <?php echo $settings['telegram_enabled'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="telegram_enabled">
                                        Enable Telegram Notifications
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="telegram_token" class="form-label">Telegram Bot Token</label>
                                <input type="text" class="form-control" id="telegram_token" name="telegram_token" value="<?php echo $settings['telegram_token']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="telegram_chat_id" class="form-label">Telegram Chat ID</label>
                                <input type="text" class="form-control" id="telegram_chat_id" name="telegram_chat_id" value="<?php echo $settings['telegram_chat_id']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="tor_enabled" name="tor_enabled" <?php echo $settings['tor_enabled'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="tor_enabled">
                                        Enable Tor Network
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="check_interval" class="form-label">Check Interval (seconds)</label>
                                <input type="number" class="form-control" id="check_interval" name="check_interval" value="<?php echo $settings['check_interval']; ?>" min="10">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <br><br>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>