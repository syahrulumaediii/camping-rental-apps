<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/middleware.php';

Middleware::guest();

$pageTitle = 'Login - ' . APP_NAME;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        setFlashMessage('danger', 'Username and password are required');
    } else {
        $auth = new Auth();
        $result = $auth->login($username, $password);

        if ($result['success']) {
            setFlashMessage('success', 'Login successful');

            // Redirect based on role
            if (Auth::isAdmin()) {
                header('Location: ' . APP_URL . '/admin/index.php');
            } else {
                header('Location: ' . APP_URL . '/index.php');
            }
            exit;
        } else {
            setFlashMessage('danger', $result['message']);
        }
    }
}

include __DIR__ . '/views/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/login.css">
</head>

<body>

</body>

</html>
<div class="login-container">
    <div class="login-card">
        <div class="row g-0">
            <!-- Left Side - Camping Theme -->
            <div class="col-lg-6 login-left d-none d-lg-block">
                <div class="particles">
                    <div class="particle" style="left: 10%; width: 10px; height: 10px; animation-delay: 0s;"></div>
                    <div class="particle" style="left: 30%; width: 8px; height: 8px; animation-delay: 2s;"></div>
                    <div class="particle" style="left: 50%; width: 12px; height: 12px; animation-delay: 4s;"></div>
                    <div class="particle" style="left: 70%; width: 9px; height: 9px; animation-delay: 6s;"></div>
                    <div class="particle" style="left: 90%; width: 11px; height: 11px; animation-delay: 8s;"></div>
                </div>

                <div style="position: relative; z-index: 1;">
                    <div class="camping-icon">⛺🏕️</div>
                    <h2><?= APP_NAME ?></h2>
                    <p>Sewa Peralatan Camping Terlengkap & Terpercaya</p>

                    <ul class="features">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Peralatan camping berkualitas tinggi</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Harga terjangkau dan fleksibel</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Booking online mudah dan cepat</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Layanan customer service 24/7</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="col-lg-6 login-right">
                <div class="login-header">
                    <h3>Selamat Datang! 👋</h3>
                    <p>Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>Username atau Email</label>
                        <div class="input-wrapper">
                            <input type="text" name="username" class="form-control"
                                placeholder="Masukkan username atau email" required autofocus>
                            <i class="bi bi-person-fill input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password"
                                required id="password">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <button type="button" class="toggle-password" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="remember-forgot">
                        <div class="form-check">
                            <input type="checkbox" id="remember">
                            <label for="remember">Ingat saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Sekarang
                    </button>

                    <div class="divider">
                        <span>ATAU</span>
                    </div>

                    <div class="register-link">
                        Belum punya akun? <a href="<?= APP_URL ?>/register.php">Daftar di sini</a>
                    </div>

                    <div class="demo-accounts">
                        <strong>🔑 Akun Demo:</strong>
                        <div>
                            <span>Admin:</span>
                            <span>admin / admin123</span>
                        </div>
                        <div>
                            <span>User:</span>
                            <span>user1 / admin123</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });

    // Add input focus animation
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // Form submission animation
    document.querySelector('form').addEventListener('submit', function(e) {
        const btn = document.querySelector('.btn-login');
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
        btn.style.opacity = '0.7';
    });
</script>

<?php include __DIR__ . '/views/footer.php'; ?>