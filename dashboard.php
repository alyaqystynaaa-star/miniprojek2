<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

requireLogin();

$user = currentUser();

if (($user['role'] ?? '') === 'admin') {
    redirect('admin.php');
}

redirect('student.php');
?>
