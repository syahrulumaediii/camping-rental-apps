<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../lib/functions.php';
Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_URL . '/admin/users.php');
    exit;
}

$action = $_POST['action'] ?? '';
$userModel = new User();

switch ($action) {
    case 'create':
        $data = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'full_name' => $_POST['full_name'],
            'phone' => $_POST['phone'] ?? null,
            'role' => $_POST['role']
        ];

        if ($userModel->create($data)) {
            setFlashMessage('success', 'User created successfully');
        } else {
            setFlashMessage('danger', 'Failed to create user');
        }
        break;

    case 'update':
        $id = $_POST['id'];
        $data = [
            'full_name' => $_POST['full_name'],
            'phone' => $_POST['phone'] ?? null,
            'role' => $_POST['role'],
            'status' => $_POST['status']
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        if ($userModel->update($id, $data)) {
            setFlashMessage('success', 'User updated successfully');
        } else {
            setFlashMessage('danger', 'Failed to update user');
        }
        break;

    case 'delete':
        $id = $_POST['id'];

        // Prevent deleting self
        if ($id == Auth::user()['id']) {
            setFlashMessage('danger', 'You cannot delete your own account');
            break;
        }

        if ($userModel->delete($id)) {
            setFlashMessage('success', 'User deleted successfully');
        } else {
            setFlashMessage('danger', 'Failed to delete user');
        }
        break;

    default:
        setFlashMessage('danger', 'Invalid action');
}

header('Location: ' . APP_URL . '/admin/users.php');
exit;
