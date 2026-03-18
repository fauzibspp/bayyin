<?php
$moduleTitle = 'Product';
$viewPath = 'products';
$data = $data ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">View <?= htmlspecialchars($moduleTitle) ?></h1>
    <div>
        <a href="/<?= htmlspecialchars($viewPath) ?>/edit?id=<?= htmlspecialchars((string) ($data['id'] ?? '')) ?>" class="btn btn-warning">
            Edit
        </a>
        <a href="/<?= htmlspecialchars($viewPath) ?>" class="btn btn-secondary">
            Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
        <tr>
            <th width="220">Name</th>
            <td><?= htmlspecialchars((string) ($data['name'] ?? '')) ?></td>
        </tr>
        <tr>
            <th width="220">Price</th>
            <td><?= htmlspecialchars((string) ($data['price'] ?? '')) ?></td>
        </tr>
        <tr>
            <th width="220">Stock</th>
            <td><?= htmlspecialchars((string) ($data['stock'] ?? '')) ?></td>
        </tr>
        <tr>
            <th width="220">Is Active</th>
            <td><?= htmlspecialchars((string) ($data['is_active'] ?? '')) ?></td>
        </tr>
        <tr>
            <th width="220">Description</th>
            <td><?= htmlspecialchars((string) ($data['description'] ?? '')) ?></td>
        </tr>
            </tbody>
        </table>
    </div>
</div>