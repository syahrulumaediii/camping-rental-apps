<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../models/Booking.php';

Auth::requireAdmin();

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

$bookingModel = new Booking();
$booking = $bookingModel->getById($id);

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

// Format data
$booking['booking_date'] = formatDate($booking['booking_date']);
$booking['start_date'] = formatDate($booking['start_date']);
$booking['end_date'] = formatDate($booking['end_date']);
$booking['total_price'] = formatRupiah($booking['total_price']);

echo json_encode(['success' => true, 'booking' => $booking]);
