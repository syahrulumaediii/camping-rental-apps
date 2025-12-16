<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../controllers/PaymentController.php';
require_once __DIR__ . '/../../lib/auth.php';

Auth::requireLogin();

$bookingId = $_GET['booking_id'] ?? null;

if (!$bookingId) {
    setFlashMessage('danger', 'Invalid booking ID');
    header('Location: ' . APP_URL . '/views/booking/status.php');
    exit;
}

$controller = new PaymentController();
$data = $controller->checkout($bookingId);

$pageTitle = 'Checkout - ' . APP_NAME;
$activePage = 'my-bookings';

include __DIR__ . '/../header.php';
include __DIR__ . '/../topnav.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-credit-card me-2"></i>Checkout</h4>
                </div>
                <div class="card-body">
                    <!-- Booking Details -->
                    <h5 class="mb-3">Booking Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <?php if (!empty($data['image_url'])): ?>
                                <img src="<?= APP_URL . '/' . htmlspecialchars($data['image_url']) ?>"
                                    class="img-fluid rounded shadow" alt="<?= htmlspecialchars($data['item_name']) ?>"
                                    style="height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                    style="height: 100px;">
                                    <i class="bi bi-image text-white fs-2"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h5><?= htmlspecialchars($data['booking']['item_name']) ?></h5>
                            <p class="text-muted mb-1">
                                <i class="bi bi-calendar-event me-1"></i>
                                <?= formatDate($data['booking']['start_date']) ?> -
                                <?= formatDate($data['booking']['end_date']) ?>
                            </p>
                            <p class="text-muted mb-1">
                                <i class="bi bi-box me-1"></i>
                                Quantity: <?= $data['booking']['quantity'] ?>
                            </p>
                            <p class="text-muted mb-0">
                                <i class="bi bi-clock me-1"></i>
                                Duration:
                                <?= calculateDays($data['booking']['start_date'], $data['booking']['end_date']) ?> hari
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Price Breakdown -->
                    <h5 class="mb-3">Price Breakdown</h5>
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Price per day:</span>
                                <strong><?= formatRupiah($data['booking']['price_per_day']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Duration:</span>
                                <strong><?= calculateDays($data['booking']['start_date'], $data['booking']['end_date']) ?>
                                    hari</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Quantity:</span>
                                <strong><?= $data['booking']['quantity'] ?></strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5>Total Amount:</h5>
                                <h5 class="text-success"><?= formatRupiah($data['booking']['total_price']) ?></h5>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <h5 class="mb-3">Payment Method</h5>
                    <form action="<?= APP_URL ?>/process_payment.php" method="POST">
                        <input type="hidden" name="booking_id" value="<?= $data['booking']['id'] ?>">

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer"
                                    value="bank_transfer" required>
                                <label class="form-check-label" for="transfer">
                                    <i class="bi bi-bank me-2"></i>Bank Transfer
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="ewallet"
                                    value="e_wallet">
                                <label class="form-check-label" for="ewallet">
                                    <i class="bi bi-wallet2 me-2"></i>E-Wallet (OVO, GoPay, Dana)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod"
                                    value="cash">
                                <label class="form-check-label" for="cod">
                                    <i class="bi bi-cash me-2"></i>Cash on Pickup
                                </label>
                            </div>
                        </div>

                        <div id="bankInfo" class="alert alert-info d-none">
                            <h6>Bank Transfer Information:</h6>
                            <p class="mb-1"><strong>Bank BCA</strong></p>
                            <p class="mb-1">Account: 1234567890</p>
                            <p class="mb-0">Name: Camping Rental</p>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and
                                    Conditions</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-check-circle me-2"></i>Confirm Payment
                        </button>
                        <a href="<?= APP_URL ?>/views/booking/status.php" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left me-2"></i>Back to Bookings
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Rental Period</h6>
                <p>The rental period is calculated based on the dates selected during booking.</p>

                <h6>2. Payment</h6>
                <p>Full payment must be completed before item pickup.</p>

                <h6>3. Cancellation</h6>
                <p>Cancellations made 24 hours before the rental start date are eligible for a full refund.</p>

                <h6>4. Item Condition</h6>
                <p>Items must be returned in the same condition as received. Damages will incur additional charges.</p>

                <h6>5. Late Returns</h6>
                <p>Late returns will be charged an additional fee per day.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const bankInfo = document.getElementById('bankInfo');
            if (this.value === 'bank_transfer') {
                bankInfo.classList.remove('d-none');
            } else {
                bankInfo.classList.add('d-none');
            }
        });
    });
</script>

<?php include __DIR__ . '/../footer.php'; ?>