<?php
$moduleTitle = 'Sample';
$viewPath = 'samples';
$items = $items ?? [];
$success = $success ?? null;
$error = $error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars($moduleTitle) ?> Trash</h1>
    <a href="/<?= htmlspecialchars($viewPath) ?>" class="btn btn-secondary">Back</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Name</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $row): ?>
                        <tr>
                            <td><?= (int) ($row['id'] ?? 0) ?></td>
                            <td><?= htmlspecialchars((string) ($row['name'] ?? '')) ?></td>
                            <td>
                                <a href="/<?= htmlspecialchars($viewPath) ?>/restore?id=<?= (int) ($row['id'] ?? 0) ?>" class="btn btn-success btn-sm">
                                    Restore
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">No deleted records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>