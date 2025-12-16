<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/middleware.php';

Middleware::guest();

$pageTitle = 'Register - ' . APP_NAME;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
        setFlashMessage('danger', 'Please fill all required fields');
    } elseif ($password !== $confirmPassword) {
        setFlashMessage('danger', 'Passwords do not match');
    } elseif (strlen($password) < 6) {
        setFlashMessage('danger', 'Password must be at least 6 characters');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('danger', 'Invalid email format');
    } else {
        $auth = new Auth();
        $result = $auth->register($username, $email, $password, $fullName, $phone);

        if ($result['success']) {
            setFlashMessage('success', 'Registration successful! Please login.');
            header('Location: ' . APP_URL . '/login.php');
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            animation: float 6s ease-in-out infinite;
            z-index: 1;
        }

        body::after {
            content: '';
            position: fixed;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            bottom: -50px;
            right: -50px;
            animation: float 8s ease-in-out infinite reverse;
            z-index: 1;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(30px);
            }
        }

        .container {
            position: relative;
            z-index: 2;
            padding: 20px;
        }

        .register-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            min-height: 100vh;
        }

        .register-form-section {
            animation: slideInRight 0.8s ease-out;
        }

        .register-info-section {
            color: white;
            animation: slideInLeft 0.8s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
        }

        .card-body {
            padding: 40px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 15px;
            display: inline-block;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .form-header h3 {
            font-size: 1.8rem;
            color: #333;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .form-header p {
            color: #999;
            font-size: 0.95rem;
        }

        .form-control,
        .form-check-input {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background-color: #fff;
        }

        .form-control:hover {
            border-color: #667eea;
        }

        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            font-size: 0.95rem;
        }

        .text-danger {
            color: #e74c3c !important;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            color: #555;
            margin-left: 8px;
            cursor: pointer;
        }

        .form-check-label a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .form-check-label a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 13px 20px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link p {
            color: #666;
            margin: 0;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .info-box:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(10px);
        }

        .info-box i {
            font-size: 2rem;
            margin-bottom: 15px;
            display: block;
        }

        .info-box h5 {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .info-box p {
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
            opacity: 0.9;
        }

        .form-text {
            color: #999 !important;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Input Group Styles */
        .mb-3 {
            margin-bottom: 20px;
        }

        .col-md-6 .mb-3,
        .col-md-12 .mb-3 {
            margin-bottom: 0;
        }

        .row {
            margin: 0 -10px;
        }

        .row>div {
            padding: 0 10px;
            margin-bottom: 25px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .register-wrapper {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .register-info-section {
                display: none;
            }

            .card-body {
                padding: 30px 20px;
            }

            .logo-icon {
                font-size: 3rem;
            }

            .form-header h3 {
                font-size: 1.5rem;
            }
        }

        /* Password strength indicator */
        .password-strength {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }

        .strength-weak {
            background: #e74c3c;
        }

        .strength-medium {
            background: #f39c12;
        }

        .strength-strong {
            background: #27ae60;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="register-wrapper">
            <!-- Info Section -->
            <div class="register-info-section">
                <h2 style="font-size: 2.5rem; margin-bottom: 30px; font-weight: 700;">Join Our Camping Community</h2>

                <div class="info-box">
                    <i class="bi bi-tent-fill"></i>
                    <h5>Explore Camping Sites</h5>
                    <p>Discover the best camping locations and outdoor adventures across the region.</p>
                </div>

                <div class="info-box">
                    <i class="bi bi-tree-fill"></i>
                    <h5>Easy Booking</h5>
                    <p>Book your camping experience in just a few clicks with our user-friendly platform.</p>
                </div>

                <div class="info-box">
                    <i class="bi bi-shield-check"></i>
                    <h5>Safe & Secure</h5>
                    <p>Your data is protected with our advanced security measures and encryption.</p>
                </div>

                <div class="info-box">
                    <i class="bi bi-people-fill"></i>
                    <h5>Community Support</h5>
                    <p>Join thousands of outdoor enthusiasts and share your camping experiences.</p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="register-form-section">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="form-header">
                            <div class="logo-icon">
                                <i class="bi bi-tree-fill"></i>
                            </div>
                            <h3><?= APP_NAME ?></h3>
                            <p>Create your account to start exploring</p>
                        </div>

                        <form method="POST" action="" id="registerForm">
                            <div class="row">
                                <!-- Full Name -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control"
                                        placeholder="Enter your full name" required
                                        value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                                </div>

                                <!-- Username -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control"
                                        placeholder="Choose a username" required
                                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="Enter your email"
                                        required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>

                                <!-- Phone -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control"
                                        placeholder="Enter your phone number"
                                        value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                                </div>

                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Create password" required minlength="6" id="password">
                                    <div class="password-strength">
                                        <div class="strength-bar" id="strengthBar"></div>
                                    </div>
                                    <small class="form-text">Min. 6 characters</small>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="confirm_password" class="form-control"
                                        placeholder="Confirm password" required minlength="6" id="confirmPassword">
                                    <small class="form-text" id="passwordMatch"></small>
                                </div>
                            </div>

                            <!-- Terms & Conditions -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#">Terms and Conditions</a>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-person-plus me-2"></i>Register
                            </button>

                            <!-- Login Link -->
                            <div class="login-link">
                                <p>Already have an account?
                                    <a href="<?= APP_URL ?>/login.php">Login here</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password Strength Indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');

        passwordInput.addEventListener('input', function() {
            const strength = getPasswordStrength(this.value);
            updateStrengthBar(strength);
        });

        function getPasswordStrength(password) {
            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[!@#$%^&*]/.test(password)) strength++;

            return strength;
        }

        function updateStrengthBar(strength) {
            const percentages = [0, 20, 40, 60, 80, 100];
            const classes = ['', 'strength-weak', 'strength-weak', 'strength-medium', 'strength-strong', 'strength-strong'];

            strengthBar.style.width = percentages[strength] + '%';
            strengthBar.className = 'strength-bar ' + classes[strength];
        }

        // Password Match Indicator
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const passwordMatchText = document.getElementById('passwordMatch');

        confirmPasswordInput.addEventListener('input', function() {
            if (this.value === '' || passwordInput.value === '') {
                passwordMatchText.textContent = '';
                passwordMatchText.style.color = '#999';
            } else if (this.value === passwordInput.value) {
                passwordMatchText.textContent = '✓ Passwords match';
                passwordMatchText.style.color = '#27ae60';
            } else {
                passwordMatchText.textContent = '✗ Passwords do not match';
                passwordMatchText.style.color = '#e74c3c';
            }
        });

        // Form Submission Validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const terms = document.getElementById('terms').checked;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (!terms) {
                e.preventDefault();
                alert('Please agree to Terms and Conditions');
                return false;
            }
        });

        // Add focus/blur effects
        const formControls = document.querySelectorAll('.form-control');
        formControls.forEach(control => {
            control.addEventListener('focus', function() {
                this.parentElement.style.opacity = '1';
            });
        });
    </script>

    <?php include __DIR__ . '/views/footer.php'; ?>
</body>

</html>