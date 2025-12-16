<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../models/User.php';

Auth::requireLogin();

$user = Auth::user();
$userModel = new User();
$userDetails = $userModel->getById($user['id']);
$userStats = $userModel->getUserStats($user['id']);

$pageTitle = 'Profile - ' . APP_NAME;
$activePage = 'profile';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $updateData = [
        'full_name' => $fullName,
        'phone' => $phone,
        'address' => $address
    ];

    // Update password if provided
    // Update password if provided
    if (!empty($currentPassword) && !empty($newPassword)) {
        if ($newPassword !== $confirmPassword) {
            setFlashMessage('danger', 'New passwords do not match');
        } elseif (strlen($newPassword) < 6) {
            setFlashMessage('danger', 'Password must be at least 6 characters');
        } elseif (!password_verify($currentPassword, $userDetails['password'])) {
            setFlashMessage('danger', 'Current password is incorrect');
            header('Location: ' . APP_URL . '/views/profile.php');
            exit;
        } else {
            // HASH PASSWORD BARU
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
    }


    if ($userModel->update($user['id'], $updateData)) {
        $_SESSION['full_name'] = $fullName;
        setFlashMessage('success', 'Profile updated successfully');
    } else {
        setFlashMessage('danger', 'Failed to update profile');
    }

    header('Location: ' . APP_URL . '/views/profile.php');
    exit;
}

include __DIR__ . '/header.php';
include __DIR__ . '/topnav.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                    </div>
                    <h4><?= htmlspecialchars($userDetails['full_name']) ?></h4>
                    <p class="text-muted mb-1">@<?= htmlspecialchars($userDetails['username']) ?></p>
                    <span class="badge bg-<?= $userDetails['role'] === 'admin' ? 'danger' : 'primary' ?>">
                        <?= ucfirst($userDetails['role']) ?>
                    </span>

                    <hr>

                    <div class="text-start">
                        <p class="mb-2">
                            <i class="bi bi-envelope me-2"></i>
                            <?= htmlspecialchars($userDetails['email']) ?>
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-telephone me-2"></i>
                            <?= htmlspecialchars($userDetails['phone'] ?? 'Not set') ?>
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-calendar me-2"></i>
                            Member since <?= formatDate($userDetails['created_at'], 'M Y') ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="mb-3">Statistics</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Bookings:</span>
                        <strong><?= $userStats['total_bookings'] ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Completed:</span>
                        <strong class="text-success"><?= $userStats['completed_bookings'] ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Spent:</span>
                        <strong class="text-primary"><?= formatRupiah($userStats['total_spent']) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <h6 class="mb-3">Personal Information</h6>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control"
                                value="<?= htmlspecialchars($userDetails['full_name']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($userDetails['username']) ?>" disabled>
                                <small class="text-muted">Username cannot be changed</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control"
                                    value="<?= htmlspecialchars($userDetails['email']) ?>" disabled>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control"
                                value="<?= htmlspecialchars($userDetails['phone'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control"
                                rows="3"><?= htmlspecialchars($userDetails['address'] ?? '') ?></textarea>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Change Password</h6>
                        <p class="text-muted small">Leave blank if you don't want to change password</p>

                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" minlength="6">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" minlength="6">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>