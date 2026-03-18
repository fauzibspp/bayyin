<?php use App\Core\Version; ?>

<h1 class="mb-4">Dashboard</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Welcome</h3>
            </div>
            <div class="card-body">
                You are logged in as
                <strong><?= htmlspecialchars($_SESSION['user'] ?? 'Guest') ?></strong>
                (<?= htmlspecialchars(strtoupper($_SESSION['role'] ?? 'guest')) ?>)
                <br>
                
                <strong>Framework: </strong> <?= htmlspecialchars(Version::getFull()) ?>
            </div>
        </div>
    </div>
</div>