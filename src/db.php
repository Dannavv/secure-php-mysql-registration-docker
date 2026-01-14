<?php
require_once 'config.php';

// Single global connection point
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Internal Server Error: Database unavailable.");
}

/**
 * Executes a prepared statement safely
 */
function db_query(string $sql, string $types = "", array $params = [])
{
    global $conn;

    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $stmt->close();

        // Log success (only for non-log queries to avoid infinite loops)
        if (strpos($sql, 'INSERT INTO app_logs') === false) {
            require_once 'logger.php';
            log_activity($conn, 'INFO', 'Query executed', $sql);
        }

        return $result !== false ? $result : true;

    } catch (Throwable $e) {
        require_once 'logger.php';
        log_activity($conn, 'ERROR', $e->getMessage(), $sql, $e->getTrace());
        throw new Exception('Database operation failed.');
    }
}