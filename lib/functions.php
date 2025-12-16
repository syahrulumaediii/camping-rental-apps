<?php
require_once __DIR__ . '/../config/database.php';

function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

function formatRupiah($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatDate($date, $format = 'd M Y')
{
    return date($format, strtotime($date));
}

function uploadImage($file)
{
    $targetDir = UPLOAD_PATH; // GUNAKAN PATH SERVER
    $relativePath = 'assets/uploads/'; // untuk disimpan ke database

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed'];
    }

    $maxSize = UPLOAD_MAX_SIZE;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = $targetDir . $filename;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $relativePath . $filename  // path untuk HTML/browser
        ];
    }

    return ['success' => false, 'message' => 'Failed to move file'];
}


function deleteImage($filename, $dir = 'assets/uploads/')
{
    $path = $dir . $filename;
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

function generateInvoiceNumber()
{
    return 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

function generateTransactionId()
{
    return 'TRX-' . date('YmdHis') . '-' . rand(1000, 9999);
}

function calculateDays($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $diff = $start->diff($end);
    return $diff->days + 1;
}

function setFlashMessage($type, $message)
{
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return ['type' => $type, 'message' => $message];
    }
    return null;
}

function isItemAvailable($itemId, $startDate, $endDate, $quantity = 1)
{
    $db = Database::getInstance();

    // Get total quantity
    $item = $db->fetchOne("SELECT quantity_total FROM items WHERE id = ?", [$itemId]);
    if (!$item) return false;

    // Get booked quantity
    $booked = $db->fetchOne("
    SELECT COALESCE(SUM(quantity), 0) AS total_booked
    FROM bookings
    WHERE item_id = ?
    AND status IN ('pending', 'confirmed')
    AND (start_date <= ? AND end_date >= ?)
    ", [$itemId, $endDate, $startDate]);

    $available = $item['quantity_total'] - ($booked['total_booked'] ?? 0);

    return $available >= $quantity;
}

function paginateData($data, $page = 1, $perPage = 10)
{
    $total = count($data);
    $totalPages = ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;

    return [
        'data' => array_slice($data, $offset, $perPage),
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => $totalPages
    ];
}
