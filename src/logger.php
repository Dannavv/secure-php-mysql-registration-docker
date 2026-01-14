<?php
require_once 'config.php';

function log_activity(
    ?mysqli $conn,
    string $level,
    string $message,
    ?string $query = null,
    ?array $trace = null
): void {
    static $in_logger = false;
    if ($in_logger || !$conn) return;
    $in_logger = true;

    try {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $bt[1] ?? $bt[0] ?? [];

        $file = $caller['file'] ?? 'unknown';
        $line = (int)($caller['line'] ?? 0);
        $func = $caller['function'] ?? 'none';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';
        $trace_json = $trace ? json_encode($trace) : null;

        // Ensure we use backticks for the reserved word `function`
        $sql = "INSERT INTO app_logs 
                (log_time, level, file, line, `function`, message, query_text, trace, ip_address, user_agent)
                VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            // This will show up in docker logs: docker logs php_app
            error_log("SQL Prepare Error: " . $conn->error);
            return;
        }

        // Mapping: 
        // s = level, s = file, i = line, s = function, s = message, 
        // s = query_text, s = trace, s = ip_address, s = user_agent 
        // Total: 9 placeholders (ssissssss)
        $stmt->bind_param(
            "ssissssss", 
            $level, 
            $file, 
            $line, 
            $func, 
            $message, 
            $query, 
            $trace_json, 
            $ip, 
            $ua
        );

        if (!$stmt->execute()) {
            error_log("SQL Execute Error: " . $stmt->error);
        }

        $stmt->close();

    } catch (Throwable $e) {
        error_log("LOGGER CRITICAL EXCEPTION: " . $e->getMessage());
    } finally {
        $in_logger = false;
    }
}