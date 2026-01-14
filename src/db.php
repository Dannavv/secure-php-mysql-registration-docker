<?php
require_once 'config.php';
require_once 'logger.php';

/**
 * SINGLE database function for the entire project
 *
 * @param string $sql   SQL query with placeholders
 * @param string $types Bind param types (e.g. "sssi")
 * @param array  $params Values to bind
 *
 * @return mixed mysqli_result|bool
 * @throws Exception
 */
function db_query($sql, $types = "", $params = []) {

    try {
        // Connect to MySQL
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Prepare statement
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception("Query preparation failed");
        }

        // Bind parameters if present
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        // Execute query
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Query execution failed");
        }

        // Fetch result if SELECT, else return true
        $result = mysqli_stmt_get_result($stmt);

        // Log successful query
        log_activity("Query executed successfully", $sql);

        return $result ?: true;

    } catch (Throwable $e) {

        // Log full error internally
        log_activity("DB ERROR: " . $e->getMessage(), $sql);

        // Do NOT expose DB errors to user
        throw new Exception("Database operation failed");
    }
}
