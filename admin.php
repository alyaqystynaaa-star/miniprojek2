<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('admin');

$user = currentUser();
$success = consumeFlash('success');
$search = trim($_GET['search'] ?? '');

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

$pageTitle = 'Admin Dashboard';
$activeNav = 'admin-dashboard';
$pageScript = 'assets/student-dashboard.js';
require __DIR__ . '/includes/header.php';
?>
<div class="card dashboard-card">
    <h1 class="title">Welcome <?= htmlspecialchars($user['name']) ?></h1>

    <?php if ($success !== null): ?>
        <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="get" action="">
        <div class="input-group search-box">
            <label for="search">Search Assignments</label>
            <input type="text" id="search" name="search" placeholder="Search assignments..." value="<?= htmlspecialchars($search) ?>" data-assignment-search>
        </div>
    </form>

    <p class="muted dashboard-note" data-loading-state hidden>Loading assignments...</p>
    <p class="muted" data-empty-state <?= $assignments === [] ? '' : 'hidden' ?>>No assignments found.</p>

    <div class="assignment-list" data-assignment-list data-api-url="assignments_api.php">
        <?php foreach ($assignments as $assignment): ?>
            <article class="assignment-item">
                <h3><?= htmlspecialchars($assignment['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($assignment['description'])) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
