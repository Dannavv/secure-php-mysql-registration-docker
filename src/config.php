<?php
// ================================
// config.php (Procedural & Secure)
// ================================

// Disable error display to users
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Database configuration (Docker)
define('DB_HOST', 'mysql');           // Docker service name
define('DB_USER', 'secure_user');
define('DB_PASS', 'StrongPassword@123');
define('DB_NAME', 'secure_app');

// Log file (inside container volume)
// define('LOG_FILE', __DIR__ . '/app.log');

// Secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
