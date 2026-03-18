<?php
use App\Core\CSRF;

$moduleTitle = 'Payment';
$viewPath = 'payments';
$old = $old ?? [];
$validationErrors = $validationErrors ?? [];
?>

<div class="mb-3">
    <h1 class="mb-0">Create <?= htmlspecialchars($moduleTitle) ?></h1>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (!empty($validationErrors) && is_array($validationErrors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($validationErrors as $fieldErrors): ?>
                <?php foreach ((array) $fieldErrors as $message): ?>
                    <li><?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/<?= htmlspecialchars($viewPath) ?>/create">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(CSRF::generate()) ?>">

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars((string) ($old['name'] ?? '')) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" name="amount" id="amount" value="<?= htmlspecialchars((string) ($old['amount'] ?? '')) ?>" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" name="status" id="status" value="<?= htmlspecialchars((string) ($old['status'] ?? '')) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="4" required><?= htmlspecialchars((string) ($old['notes'] ?? '')) ?></textarea>
            </div>
            <div class="form-group form-check">
                <input type="hidden" name="is_paid" value="0">
                <input type="checkbox" name="is_paid" id="is_paid" value="1" class="form-check-input" <?= !empty($old['is_paid']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_paid">Is Paid</label>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save
                </button>
                <a href="/<?= htmlspecialchars($viewPath) ?>" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>