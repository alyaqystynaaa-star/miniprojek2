<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$pageTitle = $pageTitle ?? 'Assignment System';
$layout = $layout ?? 'app';
$activeNav = $activeNav ?? '';
$user = currentUser();

$navItems = [];

if ($layout === 'app' && $user !== null) {
    if (($user['role'] ?? '') === 'admin') {
        $navItems = [
            ['key' => 'admin-dashboard', 'label' => 'Dashboard', 'href' => 'admin.php'],
            ['key' => 'admin-create', 'label' => 'Create Assignment', 'href' => 'admin.php#assignment-form'],
            ['key' => 'admin-submissions', 'label' => 'View All Submissions', 'href' => 'admin_submissions.php'],
            ['key' => 'logout', 'label' => 'Logout', 'href' => 'logout.php'],
        ];
    } else {
        $navItems = [
            ['key' => 'student-dashboard', 'label' => 'Dashboard', 'href' => 'student.php'],
            ['key' => 'student-submit', 'label' => 'Submit Assignment', 'href' => 'submit_assignment.php'],
            ['key' => 'student-submissions', 'label' => 'View Submissions', 'href' => 'view_submissions.php'],
            ['key' => 'logout', 'label' => 'Logout', 'href' => 'logout.php'],
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php if ($layout === 'app'): ?>
    <div class="page-layout">
        <nav class="top-nav">
            <div class="nav-inner">
                <?php foreach ($navItems as $item): ?>
                    <a class="<?= $activeNav === $item['key'] ? 'is-active' : '' ?>" href="<?= htmlspecialchars($item['href']) ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>
        <main class="content-wrap">
<?php else: ?>
    <div class="page-shell">
<?php endif; ?>
