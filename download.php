<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$user = currentUser();
$submissionId = (int) ($_GET['id'] ?? 0);

if ($submissionId <= 0) {
    exit('Invalid submission.');
}

$statement = $pdo->prepare(
    'SELECT s.id, s.user_id, s.file
     FROM submissions s
     WHERE s.id = :id
     LIMIT 1'
);
$statement->execute(['id' => $submissionId]);
$submission = $statement->fetch();

if (!$submission) {
    exit('Submission not found.');
}

if (($user['role'] ?? '') !== 'admin' && (int) $submission['user_id'] !== (int) $user['user_id']) {
    exit('Access denied.');
}

$storedFile = str_replace(['\\', '..'], ['/', ''], (string) $submission['file']);
$filePath = str_starts_with($storedFile, 'uploads/')
    ? __DIR__ . '/' . $storedFile
    : __DIR__ . '/uploads/' . basename($storedFile);

if (!is_file($filePath)) {
    exit('Uploaded file not found.');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($storedFile) . '"');
header('Content-Length: ' . (string) filesize($filePath));
readfile($filePath);
exit;
