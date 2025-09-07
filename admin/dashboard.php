<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/auth.php';

requireLogin();

$db = new Database();
$conn = $db->getConnection(); // این خط را اضافه کنید

// Get all wallets
$query = "SELECT * FROM wallets";
$stmt = $conn->prepare($query); // از $conn استفاده کنید
$stmt->execute();
$wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent activities
$query = "SELECT a.*, w.address as wallet_address FROM activities a JOIN wallets w ON a.wallet_id = w.id ORDER BY a.created_at DESC LIMIT 20";
$stmt = $conn->prepare($query); // از $conn استفاده کنید
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get settings
$query = "SELECT * FROM settings LIMIT 1";
$stmt = $conn->prepare($query); // از $conn استفاده کنید
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solana Analytics Dashboard</title>
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
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="wallets.php">
                                <i class="fas fa-wallet"></i> Wallets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh-btn">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Monitored Wallets</h5>
                                <p class="card-text"><?php echo count($wallets); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Telegram Notifications</h5>
                                <p class="card-text"><?php echo $settings['telegram_enabled'] ? 'Enabled' : 'Disabled'; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Last Update</h5>
                                <p class="card-text" id="last-update"><?php echo date('Y-m-d H:i:s'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Wallet Activities</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Wallet</th>
                                                <th>Type</th>
                                                <th>Token</th>
                                                <th>Value</th>
                                                <th>Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activities-table">
                                            <?php foreach ($activities as $activity): ?>
                                            <tr>
                                                <td><?php echo substr($activity['wallet_address'], 0, 10) . '...'; ?></td>
                                                <td>
                                                    <?php
                                                    $type = $activity['activity_type'];
                                                    $typeMap = [
                                                        'ACTIVITY_TOKEN_ADD_LIQ' => 'Add Liquidity',
                                                        'ACTIVITY_TOKEN_REMOVE_LIQ' => 'Remove Liquidity',
                                                        'ACTIVITY_SPL_INIT_MINT' => 'Create Token',
                                                        'ACTIVITY_AGG_TOKEN_SWAP' => 'Token Swap'
                                                    ];
                                                    echo $typeMap[$type] ?? $type;
                                                    ?>
                                                </td>
                                                <td><?php echo $activity['token_name']; ?></td>
                                                <td>$<?php echo number_format($activity['value'], 2); ?></td>
                                                <td><?php echo date('Y-m-d H:i:s', strtotime($activity['created_at'])); ?></td>
                                                <td>
                                                    <a href="https://solscan.io/account/<?php echo $activity['wallet_address']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-external-link-alt"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Activity Types Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="activityChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Wallet Activity</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="walletChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
     
    <br><br>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/chart.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>