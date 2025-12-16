<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/CatalogController.php';

$pageTitle = 'Catalog - ' . APP_NAME;
$activePage = 'catalog';

$controller = new CatalogController();
$data = $controller->index();
$items = $data['items'];
$categories = $data['categories'];
$filters = $data['filters'];

include __DIR__ . '/../header.php';
include __DIR__ . '/../topnav.php';
?>

<div class="container my-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-3">Catalog Peralatan Camping</h2>
            <p class="text-muted">Temukan peralatan camping terbaik untuk petualangan Anda</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <form method="GET" action="">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari peralatan..."
                            value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['category']) ?>"
                                <?= ($filters['category'] ?? '') === $cat['category'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="min_price" class="form-control" placeholder="Min Price"
                            value="<?= htmlspecialchars($filters['min_price'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="max_price" class="form-control" placeholder="Max Price"
                            value="<?= htmlspecialchars($filters['max_price'] ?? '') ?>">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Items Grid -->
    <div class="row g-4">
        <?php if (empty($items)): ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Tidak ada item yang ditemukan
            </div>
        </div>
        <?php else: ?>
        <?php foreach ($items as $item): ?>
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm">
                <?php if ($item['image_url']): ?>
                <img src="<?= APP_URL . '/' . htmlspecialchars($item['image_url']) ?>" class="img-fluid rounded shadow"
                    class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>"
                    style="height: 200px; object-fit: cover;">
                <?php else: ?>
                <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
                </div>
                <?php endif; ?>

                <div class="card-body">
                    <span class="badge bg-info mb-2"><?= htmlspecialchars($item['category']) ?></span>
                    <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                    <p class="card-text text-muted small">
                        <?= htmlspecialchars(substr($item['description'], 0, 80)) ?>...
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-primary mb-0"><?= formatRupiah($item['price_per_day']) ?>/hari</h5>
                        <span class="badge bg-success">Tersedia: <?= $item['quantity_total'] ?></span>
                    </div>
                </div>

                <div class="card-footer bg-white border-0">
                    <a href="<?= APP_URL ?>/views/catalog/detail.php?id=<?= $item['id'] ?>"
                        class="btn btn-primary w-100">
                        <i class="bi bi-eye me-2"></i>Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>