<?php
require_once 'config.php';

/**
 * Generate CSRF token (once per session)
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function csrf_verify(string $token): void
{
    global $conn;
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        require_once 'logger.php';
        log_activity($conn, 'SECURITY', 'CSRF validation failed');
        
        http_response_code(403);
        exit('Invalid CSRF Token');
    }
}