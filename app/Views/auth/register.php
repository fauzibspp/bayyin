<?php

use App\Core\CSRF;

$error = $error ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BayyinFramework - Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="/assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="/assets/adminlte/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box" style="width: 460px;">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="/login" class="h1"><b><?= App\Core\Version::getName() ?></b><?= App\Core\Version::getVersion() ?></a>
        </div>

        <div class="card-body">
            <p class="login-box-msg">Register a new membership</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="/register" autocomplete="off">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(CSRF::generate()) ?>">

                <div class="input-group mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <select name="roles" class="form-control" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="jpn">JPN</option>
                        <option value="ppd">PPD</option>
                        <option value="sekolah">Sekolah</option>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user-tag"></span></div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="number" name="jpn_id" class="form-control" placeholder="JPN ID (optional)">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-code-branch"></span></div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="number" name="ppd_id" class="form-control" placeholder="PPD ID (optional)">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-network-wired"></span></div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="text" name="state" class="form-control" placeholder="State (optional)">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-map-marker-alt"></span></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>

            <p class="mt-3 mb-1">
                <a href="/login">I already have a membership</a>
            </p>
        </div>
    </div>
</div>

<script src="/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/adminlte/js/adminlte.min.js"></script>
</body>
</html>