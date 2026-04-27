<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('student');

$user = currentUser();
$statement = $pdo->prepare(
    'SELECT s.id, s.file, a.title
     FROM submissions s
     INNER JOIN assignments a ON a.assignment_id = s.assignment_id
     WHERE s.user_id = :user_id
     ORDER BY s.id DESC'
);
$statement->execute(['user_id' => $user['user_id']]);
$submissions = $statement->fetchAll();

$pageTitle = 'My Submissions';
$activeNav = 'student-submissions';
require __DIR__ . '/includes/header.php';
?>
<div class="card dashboard-card narrow-card">
    <h1 class="title">Submissions</h1>

    <?php if ($submissions === []): ?>
        <p class="muted">You have not submitted any assignments yet.</p>
    <?php else: ?>
        <div class="submission-list">
            <?php foreach ($submissions as $submission): ?>
                <article class="submission-item">
                    <h3><?= htmlspecialchars($submission['title']) ?></h3>
                    <p><?= htmlspecialchars(basename($submission['file'])) ?></p>
                    <p><a href="download.php?id=<?= (int) $submission['id'] ?>">Open/Download</a></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
