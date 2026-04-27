<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = null;
$success = consumeFlash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (strlen($name) < 3) {
        $error = 'Name must be at least 3 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $checkStatement = $pdo->prepare('SELECT user_id FROM users WHERE email = :email LIMIT 1');
        $checkStatement->execute(['email' => $email]);

        if ($checkStatement->fetch()) {
            $error = 'Email is already registered.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStatement = $pdo->prepare(
                'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
            );
            $insertStatement->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'student',
            ]);

            $_SESSION['success'] = 'Registration successful. Please login.';
            redirect('login.php');
        }
    }
}

$pageTitle = 'Register';
$layout = 'auth';
$pageScript = 'assets/validation.js';
require __DIR__ . '/includes/header.php';
?>
<div class="card auth-card">
    <h1 class="title">Register</h1>

    <?php if ($error !== null): ?>
        <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success !== null): ?>
        <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="post" action="" data-validate="register" novalidate>
        <p class="message error hidden-message" data-client-message></p>
        <p class="helper-text text-center">Pendaftaran ini akan buat akaun student secara automatik.</p>

        <div class="input-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>

        <button class="btn btn-block" type="submit">Register</button>
    </form>

    <p class="helper-text text-center">
        Already have account? <a href="login.php">Login here</a>
    </p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
