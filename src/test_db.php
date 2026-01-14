<?php
require_once 'db.php';

// Simple SELECT test
$sql = "SELECT COUNT(*) AS total FROM users";
$result = db_query($sql);

$row = mysqli_fetch_assoc($result);
echo "Total users: " . $row['total'];
