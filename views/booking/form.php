<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../models/Item.php';

Auth::requireLogin();

$itemId = $_GET['item_id'] ?? null;

if (!$itemId) {
    header('Location: ' . APP_URL . '/index.php');
    exit;
}

$itemModel = new Item();
$item = $itemModel->getById($itemId);

if (!$item) {
    setFlashMessage('danger', 'Item not found');
    header('Location: ' . APP_URL . '/index.php');
    exit;
}

$pageTitle = 'Booking - ' . APP_NAME;
$activePage = 'catalog';

include __DIR__ . '/../header.php';
include __DIR__ . '/../topnav.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Form Booking</h4>
                </div>
                <div class="card-body">
                    <!-- Item Info -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <?php if ($item['image_url']): ?>
                                <img src="<?= APP_URL . '/' . htmlspecialchars($item['image_url']) ?>"
                                    alt="<?= htmlspecialchars($item['name']) ?>"
                                    style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                            <?php else: ?>
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                    style="height: 100px;">
                                    <i class="bi bi-image text-white fs-2"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h5><?= htmlspecialchars($item['name']) ?></h5>
                            <span class="badge bg-info"><?= htmlspecialchars($item['category']) ?></span>
                            <p class="text-muted mt-2 mb-0"><?= htmlspecialchars($item['description']) ?></p>
                            <h5 class="text-primary mt-2"><?= formatRupiah($item['price_per_day']) ?> /hari</h5>
                        </div>
                    </div>

                    <hr>

                    <!-- Booking Form -->
                    <form action="<?= APP_URL ?>/process_booking.php" method="POST" id="bookingForm">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required
                                    min="<?= date('Y-m-d') ?>" id="startDate">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required
                                    min="<?= date('Y-m-d') ?>" id="endDate">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1"
                                    max="<?= $item['quantity_available'] ?>" required id="quantity">
                                <small class="text-muted">Tersedia: <?= $item['quantity_total'] ?></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Total Hari</label>
                                <input type="text" class="form-control" id="totalDays" readonly value="0 hari">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Tambahkan catatan atau permintaan khusus..."></textarea>
                            </div>

                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="mb-3">Ringkasan Biaya</h5>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Harga per hari:</span>
                                            <strong><?= formatRupiah($item['price_per_day']) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Jumlah:</span>
                                            <strong id="displayQuantity">1</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Durasi:</span>
                                            <strong id="displayDays">0 hari</strong>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <h5>Total:</h5>
                                            <h5 class="text-primary" id="totalPrice"><?= formatRupiah(0) ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-check-circle me-2"></i>Konfirmasi Booking
                                </button>
                                <a href="<?= APP_URL ?>/views/catalog/detail.php?id=<?= $item['id'] ?>"
                                    class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const pricePerDay = <?= $item['price_per_day'] ?>;

    function calculateTotal() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const quantity = parseInt(document.getElementById('quantity').value) || 1;

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays > 0) {
                const total = pricePerDay * diffDays * quantity;

                document.getElementById('totalDays').value = diffDays + ' hari';
                document.getElementById('displayDays').textContent = diffDays + ' hari';
                document.getElementById('displayQuantity').textContent = quantity;
                document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
            }
        }
    }

    document.getElementById('startDate').addEventListener('change', function() {
        document.getElementById('endDate').min = this.value;
        calculateTotal();
    });

    document.getElementById('endDate').addEventListener('change', calculateTotal);
    document.getElementById('quantity').addEventListener('input', calculateTotal);

    // Form validation
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if (!startDate || !endDate) {
            e.preventDefault();
            alert('Mohon lengkapi tanggal booking');
            return;
        }

        if (new Date(endDate) < new Date(startDate)) {
            e.preventDefault();
            alert('Tanggal selesai harus setelah tanggal mulai');
            return;
        }
    });
</script>

<?php include __DIR__ . '/../footer.php'; ?>