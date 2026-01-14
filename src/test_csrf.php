<?php
require_once 'db.php'; // Ensures session is started via config.php
require_once 'csrf.php';

header('Content-Type: text/plain; charset=utf-8');
echo "CSRF Token: " . csrf_token();