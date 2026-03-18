<?php
use App\Core\CSRF;

$moduleTitle = 'Order';
$viewPath = 'orders';
$data = $data ?? ['id' => ''];
$validationErrors = $validationErrors ?? [];
?>

<div class="mb-3">
    <h1 class="mb-0">Edit <?= htmlspecialchars($moduleTitle) ?></h1>
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
        <form method="POST" action="/<?= htmlspecialchars($viewPath) ?>/edit">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(CSRF::generate()) ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($data['id'] ?? '')) ?>">

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars((string) ($data['name'] ?? '')) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="total">Total</label>
                <input type="number" name="total" id="total" value="<?= htmlspecialchars((string) ($data['total'] ?? '')) ?>" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" name="status" id="status" value="<?= htmlspecialchars((string) ($data['status'] ?? '')) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="4" required><?= htmlspecialchars((string) ($data['notes'] ?? '')) ?></textarea>
            </div>
            <div class="form-group form-check">
                <input type="hidden" name="is_paid" value="0">
                <input type="checkbox" name="is_paid" id="is_paid" value="1" class="form-check-input" <?= !empty($data['is_paid']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_paid">Is Paid</label>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="/<?= htmlspecialchars($viewPath) ?>" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>