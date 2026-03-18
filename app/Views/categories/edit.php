<?php
use App\Core\CSRF;

$moduleTitle = 'Category';
$viewPath = 'categories';
$data = $data ?? ['id' => ''];
?>

<div class="mb-3">
    <h1 class="mb-0">Edit <?= htmlspecialchars($moduleTitle) ?></h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/<?= htmlspecialchars($viewPath) ?>/edit">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(CSRF::generate()) ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($data['id'] ?? '')) ?>">

            <div class="form-group">
                <label for="name">Name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="<?= htmlspecialchars((string) ($data['name'] ?? '')) ?>"
                    class="form-control"
                    placeholder="Enter Name"
                   
                    required
                >
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea
                    name="description"
                    id="description"
                    class="form-control"
                    rows="4"
                    placeholder="Enter Description"
                    required
                ><?= htmlspecialchars((string) ($data['description'] ?? '')) ?></textarea>
            </div>
            <div class="form-group form-check">
                <input type="hidden" name="is_active" value="0">
                <input
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    value="1"
                    class="form-check-input"
                    <?= !empty($data['is_active']) ? 'checked' : '' ?>
                >
                <label class="form-check-label" for="is_active">Is Active</label>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="/<?= htmlspecialchars($viewPath) ?>" class="btn btn-secondary">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>