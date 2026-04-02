<?php
$moduleTitle = 'Archive';
$viewPath = 'archives';
$data = $data ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">View <?= htmlspecialchars($moduleTitle) ?></h1>
    <div>
        <a href="/<?= htmlspecialchars($viewPath) ?>" class="btn btn-secondary">Back</a>
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
            <th width="220">Code</th>
            <td><?= htmlspecialchars((string) ($data['code'] ?? '')) ?></td>
        </tr>
        <tr>
            <th width="220">Notes</th>
            <td><?= htmlspecialchars((string) ($data['notes'] ?? '')) ?></td>
        </tr>
        <tr>
            <th width="220">Is Active</th>
            <td><?= htmlspecialchars((string) ($data['is_active'] ?? '')) ?></td>
        </tr>
            </tbody>
        </table>
    </div>
</div>