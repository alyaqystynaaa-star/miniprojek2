<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('admin');

$statement = $pdo->query(
    'SELECT s.id, s.file, a.title, u.name
     FROM submissions s
     INNER JOIN assignments a ON a.assignment_id = s.assignment_id
     INNER JOIN users u ON u.user_id = s.user_id
     ORDER BY s.id DESC'
);
$submissions = $statement->fetchAll();

$pageTitle = 'All Submissions';
$activeNav = 'admin-submissions';
require __DIR__ . '/includes/header.php';
?>
<div class="card dashboard-card">
    <h1 class="title">All Submissions</h1>

    <?php if ($submissions === []): ?>
        <p class="muted">No submissions have been uploaded yet.</p>
    <?php else: ?>
        <div class="submission-list">
            <?php foreach ($submissions as $submission): ?>
                <article class="submission-item">
                    <h3><?= htmlspecialchars($submission['title']) ?></h3>
                    <p>Student: <?= htmlspecialchars($submission['name']) ?></p>
                    <p><?= htmlspecialchars(basename($submission['file'])) ?></p>
                    <p><a href="download.php?id=<?= (int) $submission['id'] ?>">Open/Download</a></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
