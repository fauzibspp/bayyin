<?php
use App\Core\Version;
$role = $_SESSION['role'] ?? 'guest';
$user = $_SESSION['user'] ?? 'Guest';
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/" class="brand-link">
        <span class="brand-text font-weight-light"><?= htmlspecialchars(Version::getFull()) ?></span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">
                    <?= htmlspecialchars($user) ?> (<?= htmlspecialchars(strtoupper($role)) ?>)
                </a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                

                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a href="/users" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Manage Users</p>
                        </a>
                    </li>                
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>