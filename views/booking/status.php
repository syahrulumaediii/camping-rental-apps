<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/BookingController.php';
require_once __DIR__ . '/../../lib/auth.php';

Auth::requireLogin();

$controller = new BookingController();
$data = $controller->myBookings();

$pageTitle = 'My Bookings - ' . APP_NAME;
$activePage = 'my-bookings';

include __DIR__ . '/../header.php';
include __DIR__ . '/../topnav.php';
?>

<div class="container my-5">
    <h2 class="mb-4">
        <i class="bi bi-bag-check me-2"></i>My Bookings
    </h2>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h5 class="text-warning"><?= $data['stats']['pending'] ?? 0 ?></h5>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h5 class="text-info"><?= $data['stats']['confirmed'] ?? 0 ?></h5>
                    <p class="text-muted mb-0">Confirmed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h5 class="text-success"><?= $data['stats']['completed'] ?? 0 ?></h5>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h5 class="text-danger"><?= $data['stats']['cancelled'] ?? 0 ?></h5>
                    <p class="text-muted mb-0">Cancelled</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= empty($data['current_status']) ? 'active' : '' ?>" href="?">All</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data['current_status'] === 'pending' ? 'active' : '' ?>"
                href="?status=pending">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data['current_status'] === 'confirmed' ? 'active' : '' ?>"
                href="?status=confirmed">Confirmed</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data['current_status'] === 'completed' ? 'active' : '' ?>"
                href="?status=completed">Completed</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data['current_status'] === 'cancelled' ? 'active' : '' ?>"
                href="?status=cancelled">Cancelled</a>
        </li>
    </ul>

    <!-- Bookings List -->
    <?php if (empty($data['bookings'])): ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>
            Belum ada booking
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($data['bookings'] as $booking): ?>
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <?php if (!empty($booking['image_url'])): ?>
                                        <img src="<?= APP_URL . '/' . htmlspecialchars($booking['image_url']) ?>"
                                            class="img-fluid rounded shadow" alt="<?= htmlspecialchars($booking['item_name']) ?>"
                                            style="height: 100px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                            style="height: 100px;">
                                            <i class="bi bi-image text-white fs-2"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-7">
                                    <h5 class="mb-2"><?= htmlspecialchars($booking['item_name']) ?></h5>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-tag me-1"></i>
                                        <?= htmlspecialchars($booking['category']) ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <?= formatDate($booking['start_date']) ?> - <?= formatDate($booking['end_date']) ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-box me-1"></i>
                                        Quantity: <?= $booking['quantity'] ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="bi bi-calendar-plus me-1"></i>
                                        Booked: <?= formatDate($booking['created_at']) ?>
                                    </p>
                                </div>

                                <div class="col-md-3 text-end">
                                    <h4 class="text-primary mb-3"><?= formatRupiah($booking['total_price']) ?></h4>

                                    <?php
                                    $statusBadge = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $badgeClass = $statusBadge[$booking['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?> mb-3">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>

                                    <?php if ($booking['payment_status']): ?>
                                        <br>
                                        <span
                                            class="badge bg-<?= $booking['payment_status'] === 'completed' ? 'success' : 'warning' ?>">
                                            Payment: <?= ucfirst($booking['payment_status']) ?>
                                        </span>
                                    <?php endif; ?>

                                    <div class="mt-3">
                                        <?php if ($booking['status'] === 'pending' && $booking['payment_status'] !== 'completed'): ?>
                                            <a href="<?= APP_URL ?>/views/payment/checkout.php?booking_id=<?= $booking['id'] ?>"
                                                class="btn btn-sm btn-primary w-100 mb-2">
                                                <i class="bi bi-credit-card me-1"></i>Pay Now
                                            </a>
                                        <?php endif; ?>

                                        <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
                                            <form action="<?= APP_URL ?>/process_cancel_booking.php" method="POST"
                                                onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../footer.php'; ?>