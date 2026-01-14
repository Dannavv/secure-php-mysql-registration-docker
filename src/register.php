<?php
require_once 'db.php'; // This now handles config and connection
require_once 'logger.php';
require_once 'csrf.php';

$success = false;
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_verify($_POST['csrf_token'] ?? '');

        // Validation
        $roll = trim($_POST['roll']);
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);
        $email = filter_var($_POST['webmail'], FILTER_VALIDATE_EMAIL);

        if (!$email) throw new Exception("Invalid email format");
        if (strlen($_POST['password']) < 8) throw new Exception("Password too short");

        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (roll, webmail, password, name, phone) VALUES (?, ?, ?, ?, ?)";
        db_query($sql, "sssss", [$roll, $email, $password, $name, $phone]);

        log_activity($conn, 'INFO', "User registered: $email");
        $success = true;

    } catch (Throwable $e) {
        $error_msg = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3>Secure User Registration</h3>

            <?php if ($success): ?>
                <div class="alert alert-success">User registered successfully!</div>
                <a href="register.php" class="btn btn-primary">Register Another</a>
            <?php else: ?>
                <?php if ($error_msg): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="mb-3"><label>Roll</label><input type="text" name="roll" class="form-control" required></div>
                    <div class="mb-3"><label>Webmail</label><input type="email" name="webmail" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control" required></div>
                    <button class="btn btn-primary w-100">Register</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>