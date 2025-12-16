<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Item.php';

Auth::requireAdmin();

$pageTitle = 'Reports - Admin';
$activePage = 'reports';

$bookingModel = new Booking();
$paymentModel = new Payment();
$itemModel = new Item();

// Date filter
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Get data
$bookingStats = $bookingModel->getBookingStats();
$paymentStats = $paymentModel->getPaymentStats();
$revenueData = $paymentModel->getRevenueByDate($startDate, $endDate);

// Calculate totals
$totalRevenue = array_sum(array_column($revenueData, 'total_revenue'));
$totalTransactions = array_sum(array_column($revenueData, 'total_transactions'));

// Calculate averages
$avgRevenuePerDay = $totalRevenue / (count($revenueData) ?: 1);
$avgTransactionsPerDay = $totalTransactions / (count($revenueData) ?: 1);

// Get top items
$db = Database::getInstance();
$topItems = $db->fetchAll("
    SELECT i.name, i.category, COUNT(b.id) as booking_count, SUM(b.total_price) as total_revenue
    FROM items i
    LEFT JOIN bookings b ON i.id = b.item_id
    WHERE b.status IN ('confirmed', 'completed')
    GROUP BY i.id
    ORDER BY booking_count DESC
    LIMIT 5
");

include __DIR__ . '/../views/header.php';
?>

<style>
    .stat-card {
        border-radius: 16px;
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
        position: relative;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .chart-card {
        border-radius: 16px;
        border: none;
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding: 15px;
        border: none;
    }

    .table-modern thead th:first-child {
        border-radius: 10px 0 0 0;
    }

    .table-modern thead th:last-child {
        border-radius: 0 10px 0 0;
    }

    .table-modern tbody tr {
        transition: all 0.3s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }

    .table-modern tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .badge-modern {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        color: white;
    }

    .filter-card .form-label {
        color: white;
        font-weight: 500;
    }

    .filter-card .form-control {
        border-radius: 10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.9);
    }

    .filter-card .btn {
        border-radius: 10px;
        font-weight: 600;
    }

    .progress-modern {
        height: 8px;
        border-radius: 10px;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .progress-modern .progress-bar {
        border-radius: 10px;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-in {
        animation: fadeInUp 0.6s ease-out;
    }

    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .rank-badge {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 10px;
    }

    .rank-1 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .rank-2 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .rank-3 {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }

    .rank-4,
    .rank-5 {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
    }

    @media print {

        .no-print,
        .filter-card,
        .page-header button {
            display: none !important;
        }
    }
</style>

<div class="d-flex">
    <?php include __DIR__ . '/../views/sidebar.php'; ?>

    <div class="flex-grow-1 p-4">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header animate-in">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-2"><i class="bi bi-graph-up-arrow me-2"></i>Dashboard Laporan & Analisis</h2>
                        <p class="mb-0 opacity-75">Monitor performa dan trend bisnis Anda secara real-time</p>
                    </div>
                    <button class="btn btn-light btn-lg" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Print Report
                    </button>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="card filter-card shadow-lg mb-4 animate-in no-print">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar-event me-2"></i>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar-check me-2"></i>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-light w-100">
                                <i class="bi bi-funnel me-2"></i>Filter Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4 g-4">
                <div class="col-md-3 animate-in" style="animation-delay: 0.1s">
                    <div class="stat-card card shadow-sm text-white"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body">
                            <div class="stat-icon bg-white bg-opacity-25">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <h6 class="opacity-75 mb-2">Total Pendapatan</h6>
                            <h2 class="mb-1"><?= formatRupiah($totalRevenue) ?></h2>
                            <small class="opacity-75"><i
                                    class="bi bi-calendar-range me-1"></i><?= formatDate($startDate) ?>
                                - <?= formatDate($endDate) ?></small>
                            <div class="mt-3">
                                <small>Rata-rata / hari: <?= formatRupiah($avgRevenuePerDay) ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 animate-in" style="animation-delay: 0.2s">
                    <div class="stat-card card shadow-sm text-white"
                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body">
                            <div class="stat-icon bg-white bg-opacity-25">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <h6 class="opacity-75 mb-2">Total Transaksi</h6>
                            <h2 class="mb-1"><?= $totalTransactions ?></h2>
                            <small class="opacity-75"><i class="bi bi-check-circle me-1"></i>Pembayaran selesai</small>
                            <div class="mt-3">
                                <small>Rata-rata / hari: <?= number_format($avgTransactionsPerDay, 1) ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 animate-in" style="animation-delay: 0.3s">
                    <div class="stat-card card shadow-sm text-white"
                        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body">
                            <div class="stat-icon bg-white bg-opacity-25">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <h6 class="opacity-75 mb-2">Total Booking</h6>
                            <h2 class="mb-1"><?= $bookingStats['total_bookings'] ?></h2>
                            <small class="opacity-75"><i class="bi bi-infinity me-1"></i>Sepanjang waktu</small>
                            <div class="mt-3">
                                <div class="progress progress-modern">
                                    <div class="progress-bar bg-white"
                                        style="width: <?= ($bookingStats['completed'] / $bookingStats['total_bookings']) * 100 ?>%">
                                    </div>
                                </div>
                                <small
                                    class="mt-1 d-block"><?= number_format(($bookingStats['completed'] / $bookingStats['total_bookings']) * 100, 1) ?>%
                                    selesai</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 animate-in" style="animation-delay: 0.4s">
                    <div class="stat-card card shadow-sm text-white"
                        style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="card-body">
                            <div class="stat-icon bg-white bg-opacity-25">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <h6 class="opacity-75 mb-2">Menunggu Konfirmasi</h6>
                            <h2 class="mb-1"><?= $bookingStats['pending'] ?></h2>
                            <small class="opacity-75"><i class="bi bi-exclamation-circle me-1"></i>Perlu
                                tindakan</small>
                            <div class="mt-3">
                                <span class="status-indicator bg-white"></span>
                                <small>Segera proses untuk kepuasan pelanggan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4 g-4">
                <div class="col-md-8 animate-in" style="animation-delay: 0.5s">
                    <div class="card chart-card shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Trend Pendapatan</h5>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary active"
                                        onclick="updateChart('bar')">Bar</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="updateChart('line')">Line</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="80"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 animate-in" style="animation-delay: 0.6s">
                    <div class="card chart-card shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Status Booking</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart" height="200"></canvas>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span><span class="status-indicator"
                                        style="background: rgb(255, 205, 86);"></span>Pending</span>
                                <strong><?= $bookingStats['pending'] ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span><span class="status-indicator"
                                        style="background: rgb(54, 162, 235);"></span>Confirmed</span>
                                <strong><?= $bookingStats['confirmed'] ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span><span class="status-indicator"
                                        style="background: rgb(75, 192, 192);"></span>Completed</span>
                                <strong><?= $bookingStats['completed'] ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><span class="status-indicator"
                                        style="background: rgb(255, 99, 132);"></span>Cancelled</span>
                                <strong><?= $bookingStats['cancelled'] ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Items -->
            <div class="row mb-4 g-4">
                <div class="col-md-12 animate-in" style="animation-delay: 0.7s">
                    <div class="card chart-card shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Top 5 Item Terpopuler</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th width="80">Rank</th>
                                            <th>Nama Item</th>
                                            <!-- <th>Kategori</th> -->
                                            <th>Jumlah Booking</th>
                                            <th>Total Pendapatan</th>
                                            <th width="200">Popularitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $maxBookings = max(array_column($topItems, 'booking_count'));
                                        foreach ($topItems as $index => $item):
                                            $popularity = ($item['booking_count'] / $maxBookings) * 100;
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="rank-badge rank-<?= $index + 1 ?>">
                                                        <?= $index + 1 ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                                                </td>
                                                <!-- <td>
                                                    <span class="badge-modern badge bg-gradient"
                                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                        <?= htmlspecialchars($item['category']) ?>
                                                    </span>
                                                </td> -->
                                                <td>
                                                    <i class="bi bi-calendar-check text-primary me-2"></i>
                                                    <strong><?= $item['booking_count'] ?></strong> bookings
                                                </td>
                                                <td>
                                                    <span class="text-success fw-bold">
                                                        <?= formatRupiah($item['total_revenue']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress progress-modern">
                                                        <div class="progress-bar"
                                                            style="width: <?= $popularity ?>%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted"><?= number_format($popularity, 1) ?>%</small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Table -->
            <div class="row">
                <div class="col-md-12 animate-in" style="animation-delay: 0.8s">
                    <div class="card chart-card shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="bi bi-table me-2 text-primary"></i>Rincian Pendapatan Harian</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-calendar3 me-2"></i>Tanggal</th>
                                            <th><i class="bi bi-receipt me-2"></i>Transaksi</th>
                                            <th><i class="bi bi-cash-stack me-2"></i>Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($revenueData)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-5">
                                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                                    <p>Tidak ada data untuk periode yang dipilih</p>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($revenueData as $data): ?>
                                                <tr>
                                                    <td><?= formatDate($data['date']) ?></td>
                                                    <td>
                                                        <span class="badge-modern badge bg-info">
                                                            <?= $data['total_transactions'] ?> transaksi
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">
                                                            <?= formatRupiah($data['total_revenue']) ?>
                                                        </strong>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr
                                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                <td><strong><i class="bi bi-calculator me-2"></i>TOTAL</strong></td>
                                                <td><strong><?= $totalTransactions ?> transaksi</strong></td>
                                                <td><strong><?= formatRupiah($totalRevenue) ?></strong></td>
                                            </tr>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let revenueChart;

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?= json_encode(array_reverse($revenueData)) ?>;

    function createChart(type) {
        if (revenueChart) {
            revenueChart.destroy();
        }

        revenueChart = new Chart(revenueCtx, {
            type: type,
            data: {
                labels: revenueData.map(d => new Date(d.date).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                })),
                datasets: [{
                    label: 'Revenue',
                    data: revenueData.map(d => d.total_revenue),
                    backgroundColor: type === 'line' ? 'rgba(102, 126, 234, 0.1)' : 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgb(102, 126, 234)',
                    borderWidth: 3,
                    fill: type === 'line',
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(102, 126, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgb(102, 126, 234)',
                        borderWidth: 1,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
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
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    createChart('bar');

    function updateChart(type) {
        createChart(type);
        // Update button active state
        document.querySelectorAll('.btn-group button').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
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
                    'rgb(255, 205, 86)',
                    'rgb(54, 162, 235)',
                    'rgb(75, 192, 192)',
                    'rgb(255, 99, 132)'
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
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderWidth: 1,
                    displayColors: true
                }
            }
        }
    });
</script>

<?php include __DIR__ . '/../views/footer.php'; ?>