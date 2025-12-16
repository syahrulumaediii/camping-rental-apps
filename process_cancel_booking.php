<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/controllers/BookingController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_URL . '/index.php');
    exit;
}

$bookingId = $_POST['booking_id'] ?? null;

if (!$bookingId) {
    setFlashMessage('danger', 'Invalid booking ID');
    header('Location: ' . APP_URL . '/views/booking/status.php');
    exit;
}

$controller = new BookingController();
$controller->cancel($bookingId);
