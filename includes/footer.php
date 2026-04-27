<?php

declare(strict_types=1);

$layout = $layout ?? 'app';
?>
<?php if ($layout === 'app'): ?>
        </main>
        <footer class="footer">&copy; 2026 Assignment System</footer>
    </div>
<?php else: ?>
    </div>
<?php endif; ?>
<?php if (!empty($pageScript)): ?>
    <script src="<?= htmlspecialchars($pageScript) ?>"></script>
<?php endif; ?>
</body>
</html>
