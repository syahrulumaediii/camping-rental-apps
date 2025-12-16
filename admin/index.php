<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/User.php';

Auth::requireAdmin();

$pageTitle = 'Dashboard - Admin';
$activePage = 'dashboard';

// Get statistics
$bookingModel = new Booking();
$paymentModel = new Payment();
$itemModel = new Item();
$userModel = new User();

$bookingStats = $bookingModel->getBookingStats();
$paymentStats = $paymentModel->getPaymentStats();
$totalItems = count($itemModel->getAll());
$totalUsers = count($userModel->getAll(['role' => 'user']));

// Recent bookings
$recentBookings = $bookingModel->getAll(['limit' => 5]);

// Revenue data for chart (last 7 days)
$revenueData = $paymentModel->getRevenueByDate(
    date('Y-m-d', strtotime('-7 days')),
    date('Y-m-d')
);

include __DIR__ . '/../views/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/homeadmin.css">
</head>

<body>

</body>

</html>
<div class="dashboard-wrapper">
    <div class="d-flex">
        <?php include __DIR__ . '/../views/sidebar.php'; ?>

        <div class="dashboard-content flex-grow-1">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <h2>
                        <i class="bi bi-speedometer2 me-3"></i>
                        Dashboard Admin
                    </h2>
                    <div class="date-info">
                        <i class="bi bi-calendar3"></i>
                        <span><?= date('l, d F Y') ?></span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4 g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="card stats-card stats-card-primary">
                            <div class="stats-card-content">
                                <div class="stats-label">Total Bookings</div>
                                <h3 class="stats-value"><?= number_format($bookingStats['total_bookings']) ?></h3>
                            </div>
                            <i class="bi bi-calendar-check-fill stats-icon"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card stats-card stats-card-success">
                            <div class="stats-card-content">
                                <div class="stats-label">Total Pendapatan</div>
                                <h3 class="stats-value" style="font-size: 1.5rem;">
                                    <?= formatRupiah($paymentStats['total_revenue']) ?></h3>
                            </div>
                            <i class="bi bi-cash-stack stats-icon"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card stats-card stats-card-info">
                            <div class="stats-card-content">
                                <div class="stats-label">Total Items</div>
                                <h3 class="stats-value"><?= number_format($totalItems) ?></h3>
                            </div>
                            <i class="bi bi-box-seam-fill stats-icon"></i>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card stats-card stats-card-warning">
                            <div class="stats-card-content">
                                <div class="stats-label">Total Users</div>
                                <h3 class="stats-value"><?= number_format($totalUsers) ?></h3>
                            </div>
                            <i class="bi bi-people-fill stats-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <div class="card chart-card">
                            <div class="card-header chart-card-header">
                                <h5><i class="bi bi-graph-up me-2"></i>Pendapatan (7 Hari Terakhir)</h5>
                            </div>
                            <div class="card-body chart-card-body">
                                <canvas id="revenueChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card chart-card">
                            <div class="card-header chart-card-header">
                                <h5><i class="bi bi-pie-chart-fill me-2"></i>Status Booking</h5>
                            </div>
                            <div class="card-body chart-card-body">
                                <canvas id="bookingStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="row">
                    <div class="col-12">
                        <div class="card table-card">
                            <div
                                class="card-header table-card-header d-flex justify-content-between align-items-center">
                                <h5><i class="bi bi-clock-history me-2"></i>Booking Terkini</h5>
                                <a href="<?= APP_URL ?>/admin/bookings.php" class="btn btn-view-all">
                                    <i class="bi bi-arrow-right-circle me-2"></i>Lihat Semua
                                </a>
                            </div>
                            <div class="card-body table-card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Item</th>
                                                <th>Periode</th>
                                                <th>Total</th>
                                                <th>Status Booking</th>
                                                <th>Status Payment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($recentBookings)): ?>
                                                <tr>
                                                    <td colspan="6">
                                                        <div class="empty-state">
                                                            <div class="empty-icon">
                                                                <i class="bi bi-inbox"></i>
                                                            </div>
                                                            <p class="mb-0">Belum ada booking terkini</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach (array_slice($recentBookings, 0, 5) as $booking): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="customer-name">
                                                                <i class="bi bi-person-circle me-2"></i>
                                                                <?= htmlspecialchars($booking['full_name']) ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="item-name">
                                                                <i class="bi bi-box me-2"></i>
                                                                <?= htmlspecialchars($booking['item_name']) ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <i class="bi bi-calendar-range me-2"></i>
                                                            <?= formatDate($booking['start_date'], 'd M') ?> -
                                                            <?= formatDate($booking['end_date'], 'd M') ?>
                                                        </td>
                                                        <td>
                                                            <span class="price-value">
                                                                <?= formatRupiah($booking['total_price']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $statusClass = [
                                                                'pending' => 'pending',
                                                                'confirmed' => 'confirmed',
                                                                'completed' => 'completed',
                                                                'cancelled' => 'cancelled'
                                                            ];
                                                            ?>
                                                            <span
                                                                class="status-badge badge-<?= $statusClass[$booking['status']] ?>">
                                                                <?= ucfirst($booking['status']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($booking['payment_status']): ?>
                                                                <span
                                                                    class="status-badge badge-<?= $booking['payment_status'] === 'completed' ? 'paid' : 'pending' ?>">
                                                                    <?= $booking['payment_status'] === 'completed' ? 'Paid' : 'Pending' ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="status-badge badge-unpaid">Unpaid</span>
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
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?= json_encode(array_reverse($revenueData)) ?>;

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.map(d => new Date(d.date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short'
            })),
            datasets: [{
                label: 'Revenue',
                data: revenueData.map(d => d.total_revenue),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointRadius: 5,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000) + 'K';
                        },
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Booking Status Chart
    const statusCtx = document.getElementById('bookingStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    <?= $bookingStats['pending'] ?>,
                    <?= $bookingStats['confirmed'] ?>,
                    <?= $bookingStats['completed'] ?>,
                    <?= $bookingStats['cancelled'] ?>
                ],
                backgroundColor: [
                    '#fbbf24',
                    '#3b82f6',
                    '#10b981',
                    '#ef4444'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            }
        }
    });

    // Add smooth animations on page load
    document.addEventListener('DOMContentLoaded', function() {
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>

<?php include __DIR__ . '/../views/footer.php'; ?>