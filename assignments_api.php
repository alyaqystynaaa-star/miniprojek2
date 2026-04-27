<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$user = currentUser();
$role = $user['role'] ?? '';

if ($role !== 'student' && $role !== 'admin') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error' => 'You are not allowed to access this resource.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$search = trim((string) ($_GET['search'] ?? ''));

if ($search !== '') {
    $statement = $pdo->prepare(
        'SELECT assignment_id, title, description
         FROM assignments
         WHERE title LIKE :search OR description LIKE :search
         ORDER BY assignment_id DESC'
    );
    $statement->execute(['search' => '%' . $search . '%']);
    $assignments = $statement->fetchAll();
} else {
    $statement = $pdo->query(
        'SELECT assignment_id, title, description
         FROM assignments
         ORDER BY assignment_id DESC'
    );
    $assignments = $statement->fetchAll();
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'assignments' => $assignments,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
