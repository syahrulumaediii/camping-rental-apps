<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php'; // ← WAJIB


class Middleware
{
    public static function guest()
    {
        if (Auth::check()) {
            redirect('/camping_rental/index.php');
        }
    }

    public static function auth()
    {
        Auth::requireLogin();
    }

    public static function admin()
    {
        Auth::requireAdmin();
    }

    public static function checkOwnership($resourceType, $resourceId, $userIdField = 'user_id')
    {
        if (Auth::isAdmin()) {
            return true;
        }

        $user = Auth::user();
        if (!$user) {
            return false;
        }

        $db = Database::getInstance();

        $resource = $db->fetchOne(
            "SELECT {$userIdField} FROM {$resourceType} WHERE id = ?",
            [$resourceId]
        );

        if (!$resource) {
            return false;
        }

        return $resource[$userIdField] == $user['id'];
    }

    public static function validateCSRF($token)
    {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            setFlashMessage('danger', 'Invalid security token');
            redirect($_SERVER['HTTP_REFERER'] ?? '/camping_rental/index.php');
        }
    }

    public static function generateCSRF()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function rateLimit($key, $maxAttempts = 5, $decayMinutes = 1)
    {
        $rateLimitKey = "rate_limit_{$key}";

        if (!isset($_SESSION[$rateLimitKey])) {
            $_SESSION[$rateLimitKey] = [
                'attempts' => 0,
                'reset_time' => time() + ($decayMinutes * 60)
            ];
        }

        $rateLimit = $_SESSION[$rateLimitKey];

        // Reset if time expired
        if (time() > $rateLimit['reset_time']) {
            $_SESSION[$rateLimitKey] = [
                'attempts' => 1,
                'reset_time' => time() + ($decayMinutes * 60)
            ];
            return true;
        }

        // Check if exceeded
        if ($rateLimit['attempts'] >= $maxAttempts) {
            return false;
        }

        // Increment attempts
        $_SESSION[$rateLimitKey]['attempts']++;
        return true;
    }
}
