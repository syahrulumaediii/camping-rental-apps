<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/controllers/CatalogController.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Home - ' . APP_NAME;
$activePage = 'catalog';

$controller = new CatalogController();
$data = $controller->index();
$items = $data['items'];
$categories = $data['categories'];
$filters = $data['filters'];

include __DIR__ . '/views/header.php';
include __DIR__ . '/views/topnav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/home.css">
</head>

<body>

</body>

</html>
<!-- Hero Section -->
<div class="hero-section text-white">
    <div class="container">
        <div class="row align-items-center hero-content">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="hero-title">Petualangan Dimulai dari Sini! 🏕️</h1>
                <p class="hero-subtitle">Sewa peralatan camping berkualitas untuk petualangan Anda. Mudah, cepat, dan
                    terpercaya!</p>
                <a href="#catalog" class="btn btn-light btn-lg hero-btn">
                    <i class="bi bi-grid me-2"></i>Jelajahi Katalog
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-icon">⛺</div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="features-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Mengapa Memilih Kami?</h2>
            <p class="text-muted mt-3">Kami berkomitmen memberikan layanan terbaik untuk petualangan Anda</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon text-success">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <h4 class="feature-title">Kualitas Terjamin</h4>
                        <p class="feature-text">Semua peralatan dalam kondisi prima dan terawat dengan standar keamanan
                            tinggi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon text-primary">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h4 class="feature-title">Harga Terjangkau</h4>
                        <p class="feature-text">Harga sewa yang kompetitif dan fleksibel sesuai dengan kebutuhan Anda
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon text-info">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <h4 class="feature-title">Proses Cepat</h4>
                        <p class="feature-text">Booking online mudah dan konfirmasi instan dalam hitungan menit</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Catalog Section -->
<div class="catalog-section" id="catalog">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Katalog Peralatan</h2>
            <p class="text-muted mt-3">Temukan peralatan camping yang sempurna untuk petualangan Anda</p>
        </div>

        <!-- Search & Filter -->
        <div class="filter-card">
            <form method="GET" action="">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <input type="text" name="search" class="form-control filter-input"
                            placeholder="🔍 Cari peralatan..."
                            value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="category" class="form-select filter-input">
                            <option value="">📂 Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['category']) ?>"
                                    <?= ($filters['category'] ?? '') === $cat['category'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <input type="number" name="min_price" class="form-control filter-input" placeholder="💰 Min"
                            value="<?= htmlspecialchars($filters['min_price'] ?? '') ?>">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <input type="number" name="max_price" class="form-control filter-input" placeholder="💰 Max"
                            value="<?= htmlspecialchars($filters['max_price'] ?? '') ?>">
                    </div>
                    <div class="col-lg-1 col-md-4">
                        <button type="submit" class="btn filter-btn w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Items Grid -->
        <div class="row g-4">
            <?php if (empty($items)): ?>
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h3 class="empty-title">Tidak Ada Item Ditemukan</h3>
                        <p class="empty-text">Coba ubah filter pencarian Anda</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card product-card">
                            <div class="product-image-wrapper">
                                <?php if ($item['image_url']): ?>
                                    <img src="<?= APP_URL . '/' . htmlspecialchars($item['image_url']) ?>"
                                        class="card-img-top product-image" alt="<?= htmlspecialchars($item['name']) ?>">
                                <?php else: ?>
                                    <div class="product-image-placeholder">
                                        <i class="bi bi-image text-white" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="product-badge">
                                    <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($item['category']) ?>
                                </span>
                            </div>

                            <div class="card-body p-4">
                                <h5 class="product-title"><?= htmlspecialchars($item['name']) ?></h5>
                                <p class="product-description">
                                    <?= htmlspecialchars(substr($item['description'], 0, 80)) ?>...
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="product-price"><?= formatRupiah($item['price_per_day']) ?><small
                                            class="text-muted" style="font-size: 0.7rem;">/hari</small></div>
                                    <span class="product-availability">
                                        <i class="bi bi-check-circle-fill me-1"></i><?= $item['quantity_available'] ?>
                                    </span>
                                </div>
                            </div>

                            <div class="card-footer bg-white border-0 p-3">
                                <a href="<?= APP_URL ?>/views/catalog/detail.php?id=<?= $item['id'] ?>"
                                    class="btn product-btn w-100 text-white">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="cta-section">
    <div class="container cta-content text-center">
        <h2 class="cta-title">Siap untuk Petualangan? 🎒</h2>
        <p class="cta-subtitle">Mulai booking peralatan camping Anda sekarang dan ciptakan kenangan tak terlupakan!</p>
        <?php if (!Auth::check()): ?>
            <div class="mt-4">
                <a href="<?= APP_URL ?>/register.php" class="btn btn-lg cta-btn cta-btn-primary">
                    <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                </a>
                <a href="<?= APP_URL ?>/login.php" class="btn btn-lg cta-btn cta-btn-outline">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </a>
            </div>
        <?php else: ?>
            <div class="mt-4">
                <a href="#catalog" class="btn btn-lg cta-btn cta-btn-primary">
                    <i class="bi bi-grid me-2"></i>Jelajahi Katalog
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.product-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        observer.observe(card);
    });

    // Parallax effect for hero icon
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const heroIcon = document.querySelector('.hero-icon');
        if (heroIcon && scrolled < 500) {
            heroIcon.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
</script>

<?php include __DIR__ . '/views/footer.php'; ?>