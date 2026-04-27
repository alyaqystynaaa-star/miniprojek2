<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('admin');

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || $description === '') {
        $error = 'Assignment title and description are required.';
    } elseif (strlen($title) < 3) {
        $error = 'Assignment title must be at least 3 characters.';
    } elseif (strlen($description) < 10) {
        $error = 'Assignment description must be at least 10 characters.';
    } else {
        $statement = $pdo->prepare(
            'INSERT INTO assignments (title, description) VALUES (:title, :description)'
        );
        $statement->execute([
            'title' => $title,
            'description' => $description,
        ]);

        setFlash('success', 'Assignment created successfully.');
        redirect('admin.php');
    }
}

$pageTitle = 'Create Assignment';
$activeNav = 'admin-create';
$pageScript = 'assets/validation.js';
require __DIR__ . '/includes/header.php';
?>
<div class="card dashboard-card narrow-card">
    <h1 class="title">Create Assignment</h1>

    <?php if ($error !== null): ?>
        <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <section class="panel submit-panel">
        <form method="post" action="" data-validate="assignment" novalidate>
            <p class="message error hidden-message" data-client-message></p>

            <div class="input-group">
                <label for="title">Assignment Title</label>
                <input type="text" id="title" name="title" placeholder="Enter assignment title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Enter assignment description" rows="6" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <button class="btn" type="submit">Create Assignment</button>
        </form>
    </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
