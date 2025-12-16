<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../models/Item.php';

Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_URL . '/admin/items.php');
    exit;
}

$action = $_POST['action'] ?? '';
$itemModel = new Item();

switch ($action) {
    case 'create':
        $imageUrl = null;

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadImage($_FILES['image']);
            if ($upload['success']) {
                $imageUrl = $upload['path'];
            }
        }

        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? null,
            'category' => $_POST['category'],
            'price_per_day' => $_POST['price_per_day'],
            'quantity_available' => $_POST['quantity_available'],
            'quantity_total' => $_POST['quantity_total'],
            'image_url' => $imageUrl,
            'status' => $_POST['status']
        ];

        if ($itemModel->create($data)) {
            setFlashMessage('success', 'Item created successfully');
        } else {
            setFlashMessage('danger', 'Failed to create item');
        }
        break;

    case 'update':
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            setFlashMessage('danger', 'Invalid item ID');
            break;
        }

        $item = $itemModel->getById($id);
        if (!$item) {
            setFlashMessage('danger', 'Item not found');
            break;
        }

        // Siapkan data dengan fallback aman
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? null),
            'category' => trim($_POST['category'] ?? ''),
            'price_per_day' => (float)($_POST['price_per_day'] ?? 0),
            'quantity_available' => (int)($_POST['quantity_available'] ?? 0),
            'quantity_total' => (int)($_POST['quantity_total'] ?? 1),
            'status' => $_POST['status'] ?? 'available',
            'image_url' => $item['image_url'] // aman karena $item pasti ada
        ];

        // Validasi minimal (opsional tapi disarankan)
        if (empty($data['name']) || empty($data['category']) || $data['price_per_day'] < 0) {
            setFlashMessage('danger', 'Name, category, and valid price are required');
            break;
        }

        // Handle image upload
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadImage($_FILES['image']);
            if ($upload['success']) {
                // Hapus gambar lama jika ada
                if (!empty($item['image_url'])) {
                    deleteImage($item['image_url']);
                }
                $data['image_url'] = $upload['path'];
            } else {
                setFlashMessage('danger', 'Image upload failed: ' . ($upload['error'] ?? 'Unknown error'));
                break;
            }
        }

        if ($itemModel->update($id, $data)) {
            setFlashMessage('success', 'Item updated successfully');
        } else {
            setFlashMessage('danger', 'Failed to update item');
        }
        break;

    case 'delete':
        $id = $_POST['id'];
        $item = $itemModel->getById($id);

        // Delete image
        if ($item['image_url']) {
            deleteImage($item['image_url']);
        }

        if ($itemModel->delete($id)) {
            setFlashMessage('success', 'Item deleted successfully');
        } else {
            setFlashMessage('danger', 'Failed to delete item');
        }
        break;

    default:
        setFlashMessage('danger', 'Invalid action');
}

header('Location: ' . APP_URL . '/admin/items.php');
exit;
