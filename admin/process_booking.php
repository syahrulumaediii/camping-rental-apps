<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../models/Booking.php';

Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_URL . '/admin/bookings.php');
    exit;
}

$action = $_POST['action'] ?? '';
$bookingId = $_POST['booking_id'] ?? null;
$status = $_POST['status'] ?? null;

if ($action === 'update_status') {
    if (empty($bookingId) || !is_numeric($bookingId) || empty($status)) {
        setFlashMessage('danger', 'Invalid input.');
    } else {
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($bookingId);
        if (!$booking) {
            setFlashMessage('danger', 'Booking not found.');
        } else {
            // ✅ Sekarang ini akan berhasil karena update() sudah diperbaiki
            if ($bookingModel->updateStatus($bookingId, $status)) {
                setFlashMessage('success', 'Booking status updated to "' . ucfirst($status) . '".');
            } else {
                setFlashMessage('danger', 'Failed to update booking.');
            }
        }
    }
}

header('Location: ' . APP_URL . '/admin/bookings.php');
exit;
