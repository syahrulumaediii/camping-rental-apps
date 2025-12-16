<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../models/Payment.php';

Auth::requireAdmin();

$pageTitle = 'Payments - Admin';
$activePage = 'payments';

$paymentModel = new Payment();

// Filter
$status = $_GET['status'] ?? null;
$payments = $paymentModel->getAll(['status' => $status]);

include __DIR__ . '/../views/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css"
        rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .d-flex {
        display: flex;
        gap: 0;
    }

    .flex-grow-1 {
        flex: 1;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 30px 40px !important;
        overflow-y: auto;
        min-height: 100vh;
    }

    .container-fluid {
        max-width: 100%;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        animation: slideDown 0.5s ease-out;
    }

    .page-header h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .page-header i {
        font-size: 2.2rem;
        color: #667eea;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Stats Cards */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 20px;
        margin-bottom: 35px;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 5px solid #667eea;
        animation: slideUp 0.6s ease-out;
    }

    .stat-card:nth-child(2) {
        border-left-color: #f39c12;
    }

    .stat-card:nth-child(3) {
        border-left-color: #27ae60;
    }

    .stat-card:nth-child(4) {
        border-left-color: #e74c3c;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-label {
        font-size: 0.85rem;
        color: #7f8c8d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-top: 10px;
        opacity: 0.2;
        float: right;
    }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        flex-wrap: wrap;
        animation: fadeIn 0.6s ease-out 0.2s both;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .nav-item {
        margin: 0;
    }

    .nav-link {
        color: #7f8c8d;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
    }

    .nav-link:hover {
        color: #667eea;
        background: rgba(102, 126, 234, 0.05);
    }

    .nav-link.active {
        color: #667eea;
        border-bottom-color: #667eea;
    }

    /* Payments Table */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        animation: slideUp 0.6s ease-out 0.3s both;
    }

    .card-body {
        padding: 0;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table thead th {
        border: none;
        padding: 18px 15px;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: rgba(102, 126, 234, 0.05);
        transform: scale(1.01);
        box-shadow: inset 0 0 10px rgba(102, 126, 234, 0.1);
    }

    .table tbody tr:last-child {
        border-bottom: none;
    }

    .table tbody td {
        padding: 16px 15px;
        vertical-align: middle;
        color: #555;
        font-size: 0.9rem;
    }

    .table code {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .customer-info {
        line-height: 1.6;
    }

    .customer-info strong {
        color: #2c3e50;
        display: block;
    }

    .customer-info small {
        color: #95a5a6;
    }

    .amount-text {
        font-weight: 700;
        color: #27ae60;
        font-size: 1rem;
    }

    /* Badges */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    .bg-secondary {
        background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%) !important;
    }

    .bg-warning {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%) !important;
        color: white !important;
    }

    .bg-success {
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%) !important;
    }

    .bg-danger {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%) !important;
    }

    /* Buttons */
    .btn-group-sm {
        display: flex;
        gap: 6px;
    }

    .btn {
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-success {
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        background: linear-gradient(135deg, #229954 0%, #1e8449 100%);
    }

    .btn-danger {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
    }

    .btn-info {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }

    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        background: linear-gradient(135deg, #2980b9 0%, #1f618d 100%);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    /* Empty State */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state i {
        font-size: 4rem;
        color: #bdc3c7;
        margin-bottom: 20px;
        display: block;
    }

    .empty-state p {
        color: #95a5a6;
        font-size: 1.1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .flex-grow-1 {
            padding: 20px !important;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .page-header h2 {
            font-size: 1.5rem;
        }

        .stats-container {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .stat-card {
            padding: 15px;
        }

        .stat-value {
            font-size: 1.3rem;
        }

        .stat-icon {
            font-size: 2rem;
        }

        .filter-tabs {
            gap: 0;
            overflow-x: auto;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .nav-link {
            padding: 10px 15px;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .table {
            font-size: 0.85rem;
        }

        .table thead th {
            padding: 12px 8px;
        }

        .table tbody td {
            padding: 12px 8px;
        }

        .btn-group-sm {
            flex-direction: column;
        }

        .btn {
            padding: 5px 10px;
            font-size: 0.75rem;
        }
    }

    /* Loading Animation */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .loading {
        animation: pulse 2s ease-in-out infinite;
    }
    </style>
</head>

<body>

    <div class="d-flex">
        <?php include __DIR__ . '/../views/sidebar.php'; ?>

        <div class="flex-grow-1">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-credit-card"></i>Manage Payments</h2>
                    <div style="display: flex; gap: 5px;">
                        <span style="font-size: 0.9rem; color: #7f8c8d; align-self: center;">
                            <i class="bi bi-calendar-event"></i>
                            <?= date('d F Y') ?>
                        </span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-label">Total Payments</div>
                        <div class="stat-value"><?= count($paymentModel->getAll()) ?></div>
                        <i class="bi bi-credit-card stat-icon"></i>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Pending</div>
                        <div class="stat-value"><?= count($paymentModel->getAll(['status' => 'pending'])) ?></div>
                        <i class="bi bi-hourglass-split stat-icon"></i>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Completed</div>
                        <div class="stat-value"><?= count($paymentModel->getAll(['status' => 'completed'])) ?></div>
                        <i class="bi bi-check-circle stat-icon"></i>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Failed</div>
                        <div class="stat-value"><?= count($paymentModel->getAll(['status' => 'failed'])) ?></div>
                        <i class="bi bi-x-circle stat-icon"></i>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs nav nav-tabs">
                    <div class="nav-item">
                        <a class="nav-link <?= empty($status) ? 'active' : '' ?>" href="?">
                            <i class="bi bi-funnel"></i> All (<?= count($paymentModel->getAll()) ?>)
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link <?= $status === 'pending' ? 'active' : '' ?>" href="?status=pending">
                            <i class="bi bi-hourglass-split"></i> Pending
                            (<?= count($paymentModel->getAll(['status' => 'pending'])) ?>)
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link <?= $status === 'completed' ? 'active' : '' ?>" href="?status=completed">
                            <i class="bi bi-check-circle"></i> Completed
                            (<?= count($paymentModel->getAll(['status' => 'completed'])) ?>)
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link <?= $status === 'failed' ? 'active' : '' ?>" href="?status=failed">
                            <i class="bi bi-x-circle"></i> Failed
                            (<?= count($paymentModel->getAll(['status' => 'failed'])) ?>)
                        </a>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Customer</th>
                                        <th>Item</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($payments)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <p>No payments found</p>
                                                <small style="color: #bdc3c7;">Try adjusting your filters</small>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($payment['transaction_id']) ?></code></td>
                                        <td>
                                            <div class="customer-info">
                                                <strong><?= htmlspecialchars($payment['full_name']) ?></strong>
                                                <small><?= htmlspecialchars($payment['email']) ?></small>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($payment['item_name']) ?></td>
                                        <td>
                                            <span class="amount-text">
                                                <?= formatRupiah($payment['amount']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= ucwords(str_replace('_', ' ', $payment['payment_method'])) ?>
                                            </span>
                                        </td>
                                        <td><?= formatDate($payment['payment_date'], 'd M Y H:i') ?></td>
                                        <td>
                                            <?php
                                                    $statusClass = [
                                                        'pending' => 'warning',
                                                        'completed' => 'success',
                                                        'failed' => 'danger'
                                                    ];
                                                    ?>
                                            <span class="badge bg-<?= $statusClass[$payment['status']] ?>">
                                                <?= ucfirst($payment['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($payment['status'] === 'pending'): ?>
                                            <div class="btn-group-sm">
                                                <form action="<?= APP_URL ?>/admin/process_payments.php" method="POST"
                                                    class="d-inline">
                                                    <input type="hidden" name="action" value="confirm">
                                                    <input type="hidden" name="payment_id"
                                                        value="<?= $payment['id'] ?>">
                                                    <button type="submit" class="btn btn-success"
                                                        onclick="return confirm('Confirm this payment?')">
                                                        <i class="bi bi-check-circle"></i> Confirm
                                                    </button>
                                                </form>
                                                <form action="<?= APP_URL ?>/admin/process_payments.php" method="POST"
                                                    class="d-inline">
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="payment_id"
                                                        value="<?= $payment['id'] ?>">
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Reject this payment?')">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                            <?php else: ?>
                                            <button class="btn btn-sm btn-info"
                                                onclick="viewPayment(<?= $payment['id'] ?>)">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
    function viewPayment(id) {
        window.location.href = `<?= APP_URL ?>/views/payment/success.php?payment_id=${id}`;
    }

    // Add ripple effect to buttons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.width = '20px';
            ripple.style.height = '20px';
            ripple.style.background = 'rgba(255, 255, 255, 0.5)';
            ripple.style.borderRadius = '50%';
            ripple.style.animation = 'ripple 0.6s ease-out';
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Smooth row hover animation
    document.querySelectorAll('.table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    </script>

    <?php include __DIR__ . '/../views/footer.php'; ?>
</body>

</html>