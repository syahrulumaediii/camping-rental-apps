<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/PaymentController.php';
require_once __DIR__ . '/../../lib/auth.php';

Auth::requireLogin();

$paymentId = $_GET['payment_id'] ?? null;

if (!$paymentId) {
    setFlashMessage('danger', 'Invalid payment ID');
    header('Location: ' . APP_URL . '/views/booking/status.php');
    exit;
}

$controller = new PaymentController();
$payment = $controller->detail($paymentId);

$pageTitle = 'Payment Success - ' . APP_NAME;

include __DIR__ . '/../header.php';
include __DIR__ . '/../topnav.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <?php if ($payment['status'] === 'completed'): ?>
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="text-success mb-3">Payment Successful!</h2>
                        <p class="lead text-muted">Your booking has been confirmed</p>
                    <?php else: ?>
                        <div class="mb-4">
                            <i class="bi bi-clock-fill text-warning" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="text-warning mb-3">Payment Pending</h2>
                        <p class="lead text-muted">Waiting for payment confirmation</p>
                    <?php endif; ?>

                    <hr class="my-4">

                    <!-- Payment Details -->
                    <div class="row text-start">
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">Transaction ID</p>
                            <h5><?= htmlspecialchars($payment['transaction_id']) ?></h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">Payment Date</p>
                            <h5><?= formatDate($payment['payment_date'], 'd M Y H:i') ?></h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">Payment Method</p>
                            <h5><?= ucwords(str_replace('_', ' ', $payment['payment_method'])) ?></h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">Total Amount</p>
                            <h5 class="text-success"><?= formatRupiah($payment['amount']) ?></h5>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Booking Details -->
                    <div class="text-start">
                        <h5 class="mb-3">Booking Details</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php if (!empty($payment['image_url'])): ?>
                                            <img src="<?= APP_URL . '/' . htmlspecialchars($payment['image_url']) ?>"
                                                class="img-fluid rounded shadow"
                                                alt="<?= htmlspecialchars($payment['item_name']) ?>"
                                                style="height: 100px; object-fit: cover;">
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h5><?= htmlspecialchars($payment['item_name']) ?></h5>
                                        <p class="mb-1">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            <?= formatDate($payment['start_date']) ?> -
                                            <?= formatDate($payment['end_date']) ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="bi bi-box me-1"></i>
                                            Quantity: <?= $payment['quantity'] ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="bi bi-clock me-1"></i>
                                            Duration: <?= calculateDays($payment['start_date'], $payment['end_date']) ?>
                                            hari
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($payment['status'] === 'pending'): ?>
                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Please complete your payment. Admin will confirm your payment within 1x24 hours.
                            <?php if ($payment['payment_method'] === 'bank_transfer'): ?>
                                <hr>
                                <strong>Bank Transfer Information:</strong><br>
                                Bank BCA - 1234567890<br>
                                Name: Camping Rental
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="<?= APP_URL ?>/views/booking/status.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-bag-check me-2"></i>View My Bookings
                        </a>
                        <a href="<?= APP_URL ?>/index.php" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-house me-2"></i>Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>