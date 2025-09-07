A comprehensive monitoring system for tracking Solana wallet activities,
providing real-time notifications via Telegram, and a web-based
dashboard for management.

## 🌟 Features

-   **Real-time Monitoring**: Tracks Solana wallet activities including:

    -   Liquidity additions/removals
    -   Token creation (minting)
    -   Token swaps
    -   Other on-chain activities

-   **Telegram Integration**: Instant notifications with inline buttons
    to view transactions on Solscan

-   **Tor Network Support**: Optional Tor routing for enhanced privacy
    and IP rotation

-   **Web Dashboard**: Admin interface for managing wallets and settings

-   **Activity Tracking**: Persistent storage of all detected activities
    with duplicate prevention

## 🏗️ Architecture

### Core Components

1.  **Bot Engine** (`bot.php`): Main monitoring script that periodically
    checks wallet activities
2.  **Web Dashboard**: Admin interface for configuration and monitoring
3.  **Database**: MySQL storage for wallets, activities, and settings
4.  **API Integration**: Solscan API for fetching wallet data

### File Structure

    ├── includes/
    │ ├── config.php # Configuration constants
    │ ├── database.php # Database connection class
    │ ├── functions.php # Utility functions
    │ ├── telegram_api.php # Telegram messaging functions
    │ └── auth.php # Authentication system
    ├── admin/
    │ ├── index.php # Login page
    │ ├── dashboard.php # Main dashboard
    │ ├── wallets.php # Wallet management
    │ ├── settings.php # System configuration
    │ └── logout.php # Logout handler
    ├── assets/ # CSS, JS, and images
    ├── install.php # Installation script
    ├── bot.php # Main monitoring bot
    └── index.php # Redirect to admin panel

## 🚀 Installation

### Prerequisites

-   PHP 7.4+ with PDO MySQL extension
-   MySQL/MariaDB database
-   (Optional) Tor service for IP rotation
-   Web server (Apache/Nginx)

### Setup Steps

1.  **Configure Database**: Update `includes/config.php` with your
    database credentials:

``` php
define('DB_HOST', 'localhost');
define('DB_NAME', 'solana_analytics');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

2.  **Run Installation**: Navigate to `install.php` in your browser to:
    -   Create necessary database tables
    -   Insert default admin user (`admin/admin`)
    -   Add sample Solana wallets
3.  **Configure Telegram (Optional)**:
    -   Create a Telegram bot via @BotFather
    -   Set the bot token in settings
    -   Configure your chat ID for notifications
4.  **Cron Job Setup**: Add the bot to run periodically:

``` bash
# Run every minute
* * * * * /usr/bin/php /path/to/your/bot.php >/dev/null 2>&1
```

## ⚙️ Configuration

-   **Telegram Notifications**: Enable/disable and configure bot
    credentials
-   **Tor Network**: Toggle Tor routing for API requests
-   **Check Interval**: Frequency of wallet checks (in seconds)
-   **Wallet Management**: Add/remove Solana wallets to monitor

### Default Wallets

The system includes four sample wallets by default:

-   `88R7vdnTSVLDmPCf6xDnEPvLiWJr6DB7MsnbZTp6srwR`
-   `9ey888bGpxRY4ReUoYgpPF9TjvHsirb89KDMshG8urmh`
-   `GeiCif4ntB7ebVkezfED888C2tjBGQc1SuNbPAjQJQeT`
-   `Lnab94JFcpd22fetiQyLYqXV1XtC99PDup2Q4Ypg5Mt`

## 🔧 Usage

### Monitoring Bot

-   Checks each wallet's activities via Solscan API
-   Detects new activities (liquidity changes, swaps, mints)
-   Sends Telegram notifications
-   Rotates IP via Tor (if enabled)
-   Logs all activities to database

### Dashboard Features

-   Real-time activity view
-   Wallet management
-   System configuration
-   Activity statistics

## 🛡️ Security

-   Password hashing with bcrypt
-   SQL injection prevention with PDO
-   Optional Tor routing for anonymity
-   Session-based authentication
-   Input validation and sanitization

## 📊 API Integration

-   Endpoint: `https://api-v2.solscan.io/v2/account/activity/dextrading`
-   Parameters: wallet address, pagination
-   Built-in delays to avoid rate limits

## 🔔 Notifications

-   **Liquidity Addition** (`ACTIVITY_TOKEN_ADD_LIQ`)
-   **Liquidity Removal** (`ACTIVITY_TOKEN_REMOVE_LIQ`)
-   **Token Creation** (`ACTIVITY_SPL_INIT_MINT`)
-   **Token Swaps** (`ACTIVITY_AGG_TOKEN_SWAP`)
-   Other activities (generic format)

## 🚨 Troubleshooting

-   **API Errors**: Check connectivity, Tor config, rate limits
-   **Telegram Issues**: Verify token, chat ID, bot permissions
-   **Database Errors**: Verify credentials and DB availability

## 📈 Performance

-   Adjust check interval based on wallet count
-   Enable Tor only if required
-   Monitor DB growth, archive old data
-   Stay within API rate limits

## 📝 License

Proprietary software. All rights reserved.

> Note: This system is for monitoring and educational purposes. Ensure
> compliance with laws and regulations.
