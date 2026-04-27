<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = consumeFlash('error');
$success = consumeFlash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $statement = $pdo->prepare('SELECT user_id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid username or password.';
        } else {
            loginUser($user);
            redirect('dashboard.php');
        }
    }
}

$pageTitle = 'Login';
$layout = 'auth';
$pageScript = 'assets/validation.js';
require __DIR__ . '/includes/header.php';
?>
<div class="card auth-card">
    <h1 class="title">Login</h1>

    <?php if ($error !== null): ?>
        <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success !== null): ?>
        <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="post" action="" data-validate="login" novalidate>
        <p class="message error hidden-message" data-client-message></p>

        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>

        <button class="btn btn-block" type="submit">Login</button>
    </form>

    <p class="helper-text text-center">
        Don't have account? <a href="register.php">Register here</a>
    </p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
