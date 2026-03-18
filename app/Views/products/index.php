<?php
$moduleTitle = 'Product';
$viewPath = 'products';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars($moduleTitle) ?> Listing</h1>
    <a href="/<?= htmlspecialchars($viewPath) ?>/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New
    </a>
</div>

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
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Is Active</th>
                    <th>Description</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="99" class="text-center text-muted">
                        No records loaded yet. Customize this module controller and listing logic.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>