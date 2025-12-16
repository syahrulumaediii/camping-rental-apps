<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // 🔴 Hanya untuk DEVELOPMENT — matikan di production

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../controllers/PaymentController.php';

Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_URL . '/admin/payments.php');
    exit;
}

$action = $_POST['action'] ?? '';
$paymentId = $_POST['payment_id'] ?? null;

// ✅ Debug sementara
error_log("process_payments: action=$action, payment_id=$paymentId");

if (empty($paymentId) || !is_numeric($paymentId)) {
    setFlashMessage('danger', 'Invalid payment ID.');
    header('Location: ' . APP_URL . '/admin/payments.php');
    exit;
}

$paymentId = (int)$paymentId;

$controller = new PaymentController();

try {
    if ($action === 'confirm') {
        $result = $controller->confirm($paymentId);
        if ($result === true) {
            setFlashMessage('success', 'Payment confirmed successfully.');
        } else {
            throw new Exception('Confirmation failed (controller returned false).');
        }
    } elseif ($action === 'reject') {
        $result = $controller->reject($paymentId);
        if ($result === true) {
            setFlashMessage('info', 'Payment rejected.');
        } else {
            throw new Exception('Rejection failed.');
        }
    } else {
        throw new Exception('Invalid action: ' . htmlspecialchars($action));
    }
} catch (Exception $e) {
    error_log("Payment process error: " . $e->getMessage());
    setFlashMessage('danger', 'Error: ' . $e->getMessage());
}

header('Location: ' . APP_URL . '/admin/payments.php');
exit;
