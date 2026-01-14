<?php
require_once 'db.php'; // This connects to DB
require_once 'logger.php';

log_activity($conn, 'INFO', 'Testing table insertion');

// Check if it actually worked
$res = mysqli_query($conn, "SELECT id, message FROM app_logs ORDER BY id DESC LIMIT 1");
$row = mysqli_fetch_assoc($res);

if ($row) {
    echo "Success! Log saved with ID: " . $row['id'] . " | Message: " . $row['message'];
} else {
    echo "Failure: Log was not saved to database. Check /var/www/html/app.log or docker logs.";
}