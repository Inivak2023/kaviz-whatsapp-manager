<?php
// config.php
session_start();

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Change for production
define('DB_PASS', '');     // Change for production
define('DB_NAME', 'kaviz_db'); // Create this database in MySQL

// App Configuration
define('WHATSAPP_NUMBER', '+94700000000'); // Update with actual number
define('APP_NAME', 'Kaviz');
define('APP_URL', 'http://kaviz.xcode.lk');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // In production, do not echo the error directly
    die("Database Connection failed: " . $e->getMessage());
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>
