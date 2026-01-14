<?php
// ========================================
// Secure User Registration (Procedural)
// ========================================

require_once 'config.php';
require_once 'db.php';
require_once 'csrf.php';

$success = false;
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // -----------------------------
        // CSRF Validation
        // -----------------------------
        csrf_verify($_POST['csrf_token'] ?? '');

        // -----------------------------
        // Input Validation & XSS Safety
        // -----------------------------
        $roll  = htmlspecialchars(trim($_POST['roll']), ENT_QUOTES, 'UTF-8');
        $name  = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars(trim($_POST['phone']), ENT_QUOTES, 'UTF-8');

        $email = filter_var($_POST['webmail'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new Exception("Invalid email");
        }

        if (strlen($_POST['password']) < 8) {
            throw new Exception("Password too short");
        }

        // -----------------------------
        // Password Hashing
        // -----------------------------
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // -----------------------------
        // Insert User (Prepared Statement)
        // -----------------------------
        $sql = "
            INSERT INTO users (roll, webmail, password, name, phone)
            VALUES (?, ?, ?, ?, ?)
        ";

        db_query($sql, "sssss", [
            $roll,
            $email,
            $password,
            $name,
            $phone
        ]);

        log_activity("User registered successfully");

        $success = true;
        $success_msg = "User registered successfully";

    } catch (Throwable $e) {

        log_activity("Registration failed: " . $e->getMessage());
        $error_msg = "Registration failed. Please try again.";
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

<h3>Secure User Registration</h3>

<!-- SUCCESS MESSAGE -->
<?php if ($success): ?>
    <div class="alert alert-success">
        <?= $success_msg ?>
    </div>
<?php endif; ?>

<!-- ERROR MESSAGE -->
<?php if ($error_msg): ?>
    <div class="alert alert-danger">
        <?= $error_msg ?>
    </div>
<?php endif; ?>

<!-- =========================
     FORM OR SUCCESS ACTION
     ========================= -->

<?php if (!$success): ?>

    <!-- REGISTRATION FORM (ONLY ON LOAD / FAILURE) -->
    <form method="POST" autocomplete="off">

        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="mb-3">
            <label>Roll</label>
            <input type="text" name="roll" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Webmail</label>
            <input type="email" name="webmail" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" minlength="8" required>
        </div>

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <button class="btn btn-primary">Register</button>
    </form>

<?php else: ?>

    <!-- REGISTER MORE OPTION (ONLY AFTER SUCCESS) -->
    <div class="mt-4">
        <a href="register.php" class="btn btn-success">
            Register More Users
        </a>
    </div>

<?php endif; ?>

</body>
</html>
