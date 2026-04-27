<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('student');

$user = currentUser();
$error = null;
$success = consumeFlash('success');

$assignments = $pdo->query('SELECT assignment_id, title FROM assignments ORDER BY assignment_id DESC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignmentId = (int) ($_POST['assignment_id'] ?? 0);
    $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip'];
    $allowedMimeTypes = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword', 'application/octet-stream'],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/octet-stream',
        ],
        'ppt' => ['application/vnd.ms-powerpoint', 'application/octet-stream'],
        'pptx' => [
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip',
            'application/octet-stream',
        ],
        'txt' => ['text/plain', 'application/octet-stream'],
        'zip' => [
            'application/zip',
            'application/x-zip-compressed',
            'multipart/x-zip',
            'application/octet-stream',
        ],
    ];

    if ($assignmentId <= 0) {
        $error = 'Please choose an assignment.';
    } elseif (!isset($_FILES['submission_file'])) {
        $error = 'Please upload a file.';
    } else {
        $assignmentStatement = $pdo->prepare(
            'SELECT assignment_id FROM assignments WHERE assignment_id = :assignment_id LIMIT 1'
        );
        $assignmentStatement->execute(['assignment_id' => $assignmentId]);
        $assignment = $assignmentStatement->fetch();

        if (!$assignment) {
            $error = 'Selected assignment does not exist.';
        } else {
            $file = $_FILES['submission_file'];
            $originalFilename = trim((string) $file['name']);
            $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));

            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $error = match ((int) $file['error']) {
                    UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File size must not exceed 5MB.',
                    UPLOAD_ERR_NO_FILE => 'Please choose a file to upload.',
                    default => 'The upload failed. Please try again.',
                };
            } elseif ($originalFilename === '' || !is_uploaded_file($file['tmp_name'])) {
                $error = 'Invalid upload detected.';
            } elseif (!in_array($extension, $allowedExtensions, true)) {
                $error = 'Allowed file types: pdf, doc, docx, ppt, pptx, zip.';
            } elseif ((int) $file['size'] <= 0) {
                $error = 'Uploaded file cannot be empty.';
            } elseif ((int) $file['size'] > 5 * 1024 * 1024) {
                $error = 'File size must not exceed 5MB.';
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = (string) $finfo->file($file['tmp_name']);

                if (!in_array($mimeType, $allowedMimeTypes[$extension], true)) {
                    $error = 'The uploaded file type does not match the selected file format.';
                }
            }

            if ($error === null) {
                $uploadDirectory = __DIR__ . '/uploads';

                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0775, true);
                }

                $storedFilename = bin2hex(random_bytes(16)) . '.' . $extension;
                $destination = $uploadDirectory . '/' . $storedFilename;
                $storedFilePath = 'uploads/' . $storedFilename;

                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    $error = 'Failed to upload the file.';
                } else {
                    try {
                        $insertStatement = $pdo->prepare(
                            'INSERT INTO submissions (assignment_id, user_id, file)
                             VALUES (:assignment_id, :user_id, :file)'
                        );
                        $insertStatement->execute([
                            'assignment_id' => $assignmentId,
                            'user_id' => $user['user_id'],
                            'file' => $storedFilePath,
                        ]);
                    } catch (Throwable $throwable) {
                        if (is_file($destination)) {
                            unlink($destination);
                        }

                        $error = 'The submission could not be saved. Please try again.';
                    }

                    if ($error === null) {
                        setFlash('success', 'Assignment submitted successfully.');
                        redirect('submit_assignment.php');
                    }
                }
            }
        }
    }
}

$pageTitle = 'Submit Assignment';
$activeNav = 'student-submit';
$pageScript = 'assets/validation.js';
require __DIR__ . '/includes/header.php';
?>
<div class="card dashboard-card narrow-card">
    <h1 class="title">Submit Assignment</h1>

    <?php if ($error !== null): ?>
        <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success !== null): ?>
        <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <section class="panel submit-panel">
        <?php if ($assignments === []): ?>
            <p class="muted">No assignments are available yet.</p>
        <?php else: ?>
            <form method="post" action="" enctype="multipart/form-data" data-validate="submission" novalidate class="submit-form">
                <p class="message error hidden-message" data-client-message></p>

                <div class="input-group">
                    <label for="assignment_id" class="sr-only">Assignment</label>
                    <select id="assignment_id" name="assignment_id" required>
                        <option value="">Select Assignment</option>
                        <?php foreach ($assignments as $assignment): ?>
                            <option value="<?= (int) $assignment['assignment_id'] ?>" <?= ((int) ($_POST['assignment_id'] ?? 0) === (int) $assignment['assignment_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($assignment['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label for="submission_file" class="sr-only">Upload File</label>
                    <input type="file" id="submission_file" name="submission_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip" required>
                    <p class="field-note">Allowed file types: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP. Maximum size: 5MB.</p>
                </div>

                <button class="btn submit-btn" type="submit">Submit</button>
            </form>
        <?php endif; ?>
    </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
