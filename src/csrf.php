<?php
require_once 'config.php';
require_once 'logger.php';

/**
 * Generate CSRF token (once per session)
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        log_activity("CSRF token generated");
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function csrf_verify($token) {
    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        log_activity("CSRF validation failed");
        die("Invalid CSRF Token");
    }
}
