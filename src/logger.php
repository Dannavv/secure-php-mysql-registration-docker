<?php
require_once 'config.php';

/**
 * Verbose application logger (procedural)
 *
 * @param string $message  Message to log
 * @param string|null $query SQL query (optional)
 */
function log_activity($message, $query = null) {

    // Get full backtrace
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

    // Identify caller (who called log_activity)
    $caller = $trace[1] ?? $trace[0];

    $log_data = [
        'time'     => date('Y-m-d H:i:s'),
        'file'     => $caller['file'] ?? 'unknown',
        'line'     => $caller['line'] ?? 'unknown',
        'function' => $caller['function'] ?? 'global',
        'message'  => $message,
        'query'    => $query,
        'trace'    => $trace
    ];

    // Write JSON log entry
    file_put_contents(
        LOG_FILE,
        json_encode($log_data, JSON_PRETTY_PRINT) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}
