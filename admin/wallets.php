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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'];
    $name = $_POST['name'];
    
    $query = "INSERT INTO wallets (address, name) VALUES (:address, :name)";
    $stmt = $conn->prepare($query); // از $conn استفاده کنید
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':name', $name);
    
    if ($stmt->execute()) {
        $success = "Wallet added successfully!";
        header('Location: wallets.php');
        exit;
    } else {
        $error = "Failed to add wallet!";
    }
}

// Handle wallet deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $query = "DELETE FROM wallets WHERE id = :id";
    $stmt = $conn->prepare($query); // از $conn استفاده کنید
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        $success = "Wallet deleted successfully!";
        header('Location: wallets.php');
        exit;
    } else {
        $error = "Failed to delete wallet!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallets - Solana Analytics Dashboard</title>
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
                            <a class="nav-link active" href="wallets.php">
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
                    <h1 class="h2">Wallet Management</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWalletModal">
                        <i class="fas fa-plus"></i> Add Wallet
                    </button>
                </div>

                <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Monitored Wallets</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Added</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($wallets as $wallet): ?>
                                    <tr>
                                        <td><?php echo $wallet['id']; ?></td>
                                        <td><?php echo $wallet['name']; ?></td>
                                        <td><?php echo $wallet['address']; ?></td>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($wallet['created_at'])); ?></td>
                                        <td>
                                            <a href="https://solscan.io/account/<?php echo $wallet['address']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt"></i> View
                                            </a>
                                            <a href="?delete=<?php echo $wallet['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this wallet?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Wallet Modal -->
    <div class="modal fade" id="addWalletModal" tabindex="-1" aria-labelledby="addWalletModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addWalletModalLabel">Add New Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Wallet Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Wallet Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Wallet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>