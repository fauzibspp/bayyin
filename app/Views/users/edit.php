<?php

use App\Core\CSRF;
$errors = $validationErrors ?? [];
?>

<h1 class="mb-3">Edit User</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/components/validation-errors.php'; ?>

<div class="card">
    <div class="card-body">
        <form method="post" action="/users/edit?id=<?= (int)$user['id'] ?>">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(CSRF::generate()) ?>">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required
                       value="<?= htmlspecialchars($user['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Password <small>(leave blank to keep existing password)</small></label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="roles" class="form-control" required>
                    <?php foreach (['admin', 'jpn', 'ppd', 'sekolah'] as $role): ?>
                        <option value="<?= $role ?>" <?= (($user['roles'] ?? '') === $role) ? 'selected' : '' ?>>
                            <?= strtoupper($role) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>JPN ID</label>
                <input type="number" name="jpn_id" class="form-control"
                       value="<?= htmlspecialchars((string)($user['jpn_id'] ?? '')) ?>">
            </div>

            <div class="form-group">
                <label>PPD ID</label>
                <input type="number" name="ppd_id" class="form-control"
                       value="<?= htmlspecialchars((string)($user['ppd_id'] ?? '')) ?>">
            </div>

            <div class="form-group">
                <label>State</label>
                <input type="text" name="state" class="form-control"
                       value="<?= htmlspecialchars((string)($user['state'] ?? '')) ?>">
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/users" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>