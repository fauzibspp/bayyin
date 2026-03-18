<h1 class="mb-3">Users Management</h1>

<?php require dirname(__DIR__) . '/components/flash.php'; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Users List</h3>
        <a href="/users/create" class="btn btn-primary btn-sm">Add User</a>
    </div>

    <div class="card-body">
        <form method="get" action="/users" class="mb-3">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search name, email, role, state"
                       value="<?= htmlspecialchars($keyword ?? '') ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-info">Search</button>
                    <a href="/users" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="60">ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>State</th>
                    <th width="180">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= (int)$item['id'] ?></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['email']) ?></td>
                            <td><?= htmlspecialchars(strtoupper($item['roles'])) ?></td>
                            <td><?= htmlspecialchars((string)($item['state'] ?? '-')) ?></td>
                            <td>
                                <a href="/users/edit?id=<?= (int)$item['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="/users/delete?id=<?= (int)$item['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-3 d-flex justify-content-between">
            <div>
                Total: <strong><?= (int)$meta['total'] ?></strong>
            </div>
            <div>
                <?php
                $querySuffix = !empty($keyword) ? '&q=' . urlencode($keyword) : '';
                ?>
                <?php if ($meta['has_prev']): ?>
                    <a class="btn btn-secondary btn-sm" href="/users?page=<?= (int)$meta['prev_page'] . $querySuffix ?>">Previous</a>
                <?php endif; ?>

                <span class="mx-2">Page <?= (int)$meta['page'] ?> / <?= (int)$meta['last_page'] ?></span>

                <?php if ($meta['has_next']): ?>
                    <a class="btn btn-secondary btn-sm" href="/users?page=<?= (int)$meta['next_page'] . $querySuffix ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>