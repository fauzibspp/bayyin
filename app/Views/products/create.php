<?php
use App\Core\CSRF;

$moduleTitle = 'Product';
$viewPath = 'products';
?>

<div class="mb-3">
    <h1 class="mb-0">Create <?= htmlspecialchars($moduleTitle) ?></h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/<?= htmlspecialchars($viewPath) ?>/create">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(CSRF::generate()) ?>">

            <div class="form-group">
                <label for="name">Name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control"
                    placeholder="Enter Name"
                   
                    required
                >
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input
                    type="number"
                    name="price"
                    id="price"
                    class="form-control"
                    placeholder="Enter Price"
                    step="0.01"
                    required
                >
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input
                    type="number"
                    name="stock"
                    id="stock"
                    class="form-control"
                    placeholder="Enter Stock"
                   
                    required
                >
            </div>
            <div class="form-group form-check">
                <input type="hidden" name="is_active" value="0">
                <input
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    value="1"
                    class="form-check-input"
                >
                <label class="form-check-label" for="is_active">Is Active</label>
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
                ></textarea>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save
                </button>
                <a href="/<?= htmlspecialchars($viewPath) ?>" class="btn btn-secondary">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>