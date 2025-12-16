<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/CatalogController.php';
require_once __DIR__ . '/../../lib/auth.php';

$itemId = $_GET['id'] ?? null;

if (!$itemId) {
    header('Location: ' . APP_URL . '/index.php');
    exit;
}

$controller = new CatalogController();
$item = $controller->detail($itemId);

$pageTitle = $item['name'] . ' - ' . APP_NAME;
$activePage = 'catalog';

include __DIR__ . '/../header.php';
include __DIR__ . '/../topnav.php';
?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/index.php">Catalog</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($item['name']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Image -->
        <div class="col-md-6">
            <?php if ($item['image_url']): ?>
                <img src="<?= APP_URL . '/' . htmlspecialchars($item['image_url']) ?>" class="img-fluid rounded shadow"
                    alt="<?= htmlspecialchars($item['name']) ?>">
            <?php else: ?>
                <div class="bg-secondary d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                    <i class="bi bi-image text-white" style="font-size: 5rem;"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- Details -->
        <div class="col-md-6">
            <span class="badge bg-info mb-2"><?= htmlspecialchars($item['category']) ?></span>
            <h2 class="mb-3"><?= htmlspecialchars($item['name']) ?></h2>

            <div class="mb-3">
                <?php if ($item['total_reviews'] > 0): ?>
                    <div class="d-flex align-items-center">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i
                                class="bi bi-star-fill <?= $i <= round($item['avg_rating']) ? 'text-warning' : 'text-muted' ?>"></i>
                        <?php endfor; ?>
                        <span class="ms-2">(<?= $item['total_reviews'] ?> reviews)</span>
                    </div>
                <?php else: ?>
                    <span class="text-muted">Belum ada review</span>
                <?php endif; ?>
            </div>

            <h3 class="text-primary mb-3"><?= formatRupiah($item['price_per_day']) ?> <small
                    class="text-muted">/hari</small></h3>

            <div class="mb-3">
                <span class="badge bg-<?= $item['quantity_available'] > 0 ? 'success' : 'danger' ?> fs-6">
                    <?= $item['quantity_available'] > 0 ? 'Tersedia: ' . $item['quantity_available'] : 'Tidak Tersedia' ?>
                </span>
            </div>

            <p class="lead"><?= nl2br(htmlspecialchars($item['description'])) ?></p>

            <hr>

            <!-- Booking Form -->
            <?php if (Auth::check()): ?>
                <form action="<?= APP_URL ?>/public/process_booking.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>"
                                id="startDate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d') ?>"
                                id="endDate">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1"
                                max="<?= $item['quantity_available'] ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg w-100"
                                <?= $item['quantity_total'] <= 0 ? 'disabled' : '' ?>>
                                <i class="bi bi-cart-plus me-2"></i>Booking Sekarang
                            </button>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Silakan <a href="<?= APP_URL ?>/login.php">login</a> untuk melakukan booking
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reviews -->
    <?php if (!empty($item['reviews'])): ?>
        <div class="row mt-5">
            <div class="col-md-12">
                <h4 class="mb-4">Customer Reviews</h4>
                <?php foreach ($item['reviews'] as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($review['full_name'] ?? $review['username']) ?></h6>
                                    <div class="mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i
                                                class="bi bi-star-fill <?= $i <= $review['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <small class="text-muted"><?= formatDate($review['created_at']) ?></small>
                            </div>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.getElementById('startDate').addEventListener('change', function() {
        document.getElementById('endDate').min = this.value;
    });
</script>

<?php include __DIR__ . '/../footer.php'; ?>