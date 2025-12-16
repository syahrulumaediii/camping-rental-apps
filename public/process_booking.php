<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../controllers/BookingController.php';

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

// Wajib login
if (!Auth::check()) {
    setFlashMessage('warning', 'Silakan login terlebih dahulu.');
    header('Location: ' . APP_URL . '/login.php');
    exit;
}

try {
    $controller = new BookingController();
    $result = $controller->create(); // asumsi method create() handle $_POST

    if ($result === true || (is_array($result) && !empty($result['success']))) {
        setFlashMessage('success', 'Booking berhasil! Silakan cek riwayat booking Anda.');
        header('Location: ' . APP_URL . '/bookings.php'); // atau /bookings.php
    } else {
        $error = is_string($result) ? $result : 'Gagal membuat booking.';
        setFlashMessage('danger', $error);
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? APP_URL . '/catalog/');
    }
} catch (Exception $e) {
    error_log("Booking error: " . $e->getMessage());
    setFlashMessage('danger', 'Terjadi kesalahan sistem. Silakan coba lagi.');
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? APP_URL . '/catalog/');
}

exit;
