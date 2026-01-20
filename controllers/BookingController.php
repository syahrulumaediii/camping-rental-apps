<?php
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../config/database.php';

class BookingController
{
    private $bookingModel;
    private $itemModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->itemModel = new Item();
    }

    public function create()
    {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/camping-rental-apps/index.php');
        }

        $user = Auth::user();
        $itemId = $_POST['item_id'] ?? null;
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);
        $notes = $_POST['notes'] ?? null;

        // Validation
        if (!$itemId || !$startDate || !$endDate || $quantity < 1) {
            setFlashMessage('danger', 'Please fill all required fields');
            redirect($_SERVER['HTTP_REFERER'] ?? '/camping-rental-apps/index.php');
        }

        // Validate dates
        if (strtotime($startDate) < strtotime(date('Y-m-d'))) {
            setFlashMessage('danger', 'Start date cannot be in the past');
            redirect($_SERVER['HTTP_REFERER']);
        }

        if (strtotime($endDate) < strtotime($startDate)) {
            setFlashMessage('danger', 'End date must be after start date');
            redirect($_SERVER['HTTP_REFERER']);
        }

        // Check item exists
        $item = $this->itemModel->getById($itemId);
        if (!$item) {
            setFlashMessage('danger', 'Item not found');
            redirect('/camping-rental-apps/index.php');
        }

        // Check availability
        if (!isItemAvailable($itemId, $startDate, $endDate, $quantity)) {
            setFlashMessage('danger', 'Item is not available for selected dates');
            redirect($_SERVER['HTTP_REFERER']);
        }

        // Create booking
        $bookingData = [
            'user_id' => $user['id'],
            'item_id' => $itemId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'quantity' => $quantity,
            'notes' => $notes
        ];

        $bookingId = $this->bookingModel->create($bookingData);

        if ($bookingId) {
            setFlashMessage('success', 'Booking created successfully');
            redirect("/camping-rental-apps/views/payment/checkout.php?booking_id=$bookingId");
        } else {
            setFlashMessage('danger', 'Failed to create booking');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function myBookings()
    {
        Auth::requireLogin();

        $user = Auth::user();
        $status = $_GET['status'] ?? null;

        $bookings = $this->bookingModel->getUserBookings($user['id'], $status);
        $stats = $this->bookingModel->getBookingStats($user['id']);

        return [
            'bookings' => $bookings,
            'stats' => $stats,
            'current_status' => $status
        ];
    }

    public function detail($id)
    {
        Auth::requireLogin();

        $user = Auth::user();
        $booking = $this->bookingModel->getById($id);

        if (!$booking) {
            setFlashMessage('danger', 'Booking not found');
            redirect('/camping-rental-apps/views/booking/status.php');
        }

        // Check if user owns the booking or is admin
        if ($booking['user_id'] != $user['id'] && !Auth::isAdmin()) {
            setFlashMessage('danger', 'Unauthorized access');
            redirect('/camping-rental-apps/views/booking/status.php');
        }

        return $booking;
    }

    public function cancel($id)
    {
        Auth::requireLogin();

        $user = Auth::user();
        $booking = $this->bookingModel->getById($id);

        if (!$booking) {
            setFlashMessage('danger', 'Booking not found');
            redirect('/camping-rental-apps/views/booking/status.php');
        }

        // Check if user owns the booking
        if ($booking['user_id'] != $user['id'] && !Auth::isAdmin()) {
            setFlashMessage('danger', 'Unauthorized access');
            redirect('/camping-rental-apps/views/booking/status.php');
        }

        // Cancel booking
        if ($this->bookingModel->cancel($id, $user['id'])) {
            setFlashMessage('success', 'Booking cancelled successfully');
        } else {
            setFlashMessage('danger', 'Failed to cancel booking. Only pending or confirmed bookings can be cancelled.');
        }

        redirect('/camping-rental-apps/views/booking/status.php');
    }

    public function updateStatus($id, $status)
    {
        Auth::requireAdmin();

        $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            setFlashMessage('danger', 'Invalid status');
            redirect($_SERVER['HTTP_REFERER'] ?? '/camping-rental-apps/admin/bookings.php');
        }

        if ($this->bookingModel->updateStatus($id, $status)) {
            setFlashMessage('success', 'Booking status updated successfully');
        } else {
            setFlashMessage('danger', 'Failed to update booking status');
        }

        redirect($_SERVER['HTTP_REFERER'] ?? '/camping-rental-apps/admin/bookings.php');
    }
}
