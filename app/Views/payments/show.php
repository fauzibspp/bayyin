<?php
$moduleTitle = 'Payment';
$viewPath = 'payments';
$data = $data ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">View <?= htmlspecialchars($moduleTitle) ?></h1>
    <div>
        <a href="/<?= htmlspecialchars($viewPath) ?>/edit?id=<?= htmlspecialchars((string) ($data['id'] ?? '')) ?>" class="btn btn-warning">Edit</a>
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
            <th width="220">Amount</th>
            <td><?= htmlspecialchars((string) ($data['amount'] ?? '')) ?></td>
        </tr>
        <tr>
            <th width="220">Status</th>
            <td><?= htmlspecialchars((string) ($data['status'] ?? '')) ?></td>
        </tr>
        <tr>
            <th width="220">Notes</th>
            <td><?= htmlspecialchars((string) ($data['notes'] ?? '')) ?></td>
        </tr>
        <tr>
            <th width="220">Is Paid</th>
            <td><?= htmlspecialchars((string) ($data['is_paid'] ?? '')) ?></td>
        </tr>
            </tbody>
        </table>
    </div>
</div>