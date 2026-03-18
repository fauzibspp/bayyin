<?php
$moduleTitle = 'Order';
$viewPath = 'orders';
$success = $success ?? null;
$error = $error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars($moduleTitle) ?> Listing</h1>
    <a href="/<?= htmlspecialchars($viewPath) ?>/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New
    </a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0"><?= htmlspecialchars($moduleTitle) ?> Records</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Name</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Is Paid</th>
                    <th width="260">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="99" class="text-center text-muted">
                        Generated module ready. Connect controller listing data for live records.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>