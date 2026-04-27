<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function redirect(string $path): void
{
    header("Location: {$path}");
    exit;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Please login to continue.';
        redirect('login.php');
    }
}

function requireRole(string $role): void
{
    requireLogin();

    $user = currentUser();
    if (($user['role'] ?? '') !== $role) {
        $_SESSION['error'] = 'You are not allowed to access that page.';
        redirect('dashboard.php');
    }
}

function loginUser(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'user_id' => $user['user_id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
}

function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function consumeFlash(string $key): ?string
{
    if (!isset($_SESSION[$key])) {
        return null;
    }

    $message = $_SESSION[$key];
    unset($_SESSION[$key]);

    return $message;
}

function setFlash(string $key, string $message): void
{
    $_SESSION[$key] = $message;
}
