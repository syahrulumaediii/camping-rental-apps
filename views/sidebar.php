<?php
$menuFile = __DIR__ . '/../config/menu.json';
$menuData = json_decode(file_get_contents($menuFile), true);
$userRole = Auth::isAdmin() ? 'admin' : 'user';
$menuItems = $menuData[$userRole] ?? [];
$currentPage = $activePage ?? '';
?>

<div class="sidebar bg-dark vh-100 position-sticky top-0">
    <div class="p-3 border-bottom">
        <h5 class="text-white mb-0">
            <i class="bi bi-tree-fill me-2"></i>
            <?= APP_NAME ?>
        </h5>
    </div>

    <div class="list-group list-group-flush">
        <?php foreach ($menuItems as $item): ?>
            <a href="<?= APP_URL . $item['url'] ?>"
                class="list-group-item list-group-item-action bg-dark text-white border-0 <?= $currentPage === $item['active'] ? 'active' : '' ?>">
                <i class="bi <?= $item['icon'] ?> me-2"></i>
                <?= $item['title'] ?>
            </a>
        <?php endforeach; ?>

        <div class="mt-auto border-top pt-3">
            <a href="<?= APP_URL ?>/logout.php"
                class="list-group-item list-group-item-action bg-dark text-white border-0">
                <i class="bi bi-box-arrow-right me-2"></i>
                Logout
            </a>
        </div>
    </div>
</div>

<style>
    .sidebar {
        width: 250px;
        min-height: 100vh;
    }

    .sidebar .list-group-item:hover {
        background-color: #495057 !important;
    }

    .sidebar .list-group-item.active {
        background-color: #0d6efd !important;
    }
</style>