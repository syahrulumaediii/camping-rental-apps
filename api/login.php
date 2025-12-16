<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Rest of your code...
/**
 * API Login Endpoint
 * File: api/login.php
 * 
 * Method: POST
 * Content-Type: application/json
 */

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed. Only POST is accepted.'
    ]);
    exit();
}

// Include database
require_once __DIR__ . '/../config/database.php';

/**
 * Generate JWT Token
 */
function generateToken($user)
{
    $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
    $payload = base64_encode(json_encode([
        'user_id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
        'iat' => time(),
        'exp' => time() + (3600 * 24) // 24 jam
    ]));

    $secret = getenv('JWT_SECRET') ?: 'your_secret_key_change_this_in_production';
    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));

    return "$header.$payload.$signature";
}

/**
 * Sanitize Input
 */
function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate SQL Injection Pattern
 */
function hasSqlInjection($input)
{
    $patterns = ["'", '"', '--', ';', 'OR', 'UNION', 'SELECT', 'DROP', 'INSERT', 'UPDATE', 'DELETE', '/*', '*/'];
    foreach ($patterns as $pattern) {
        if (stripos($input, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

try {
    // Get Database instance
    $db = Database::getInstance();

    // Read JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validate JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid JSON format'
        ]);
        exit();
    }

    // Check required fields
    if (empty($data['username']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Username dan password harus diisi'
        ]);
        exit();
    }

    // Sanitize input
    $username = sanitizeInput($data['username']);
    $password = sanitizeInput($data['password']);

    // Validate length
    if (strlen($username) < 3 || strlen($username) > 50) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Username harus antara 3-50 karakter'
        ]);
        exit();
    }

    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Password minimal 6 karakter'
        ]);
        exit();
    }

    // Check SQL injection
    if (hasSqlInjection($username)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid username format'
        ]);
        exit();
    }

    // Query user from database
    $sql = "SELECT id, username, password, role, email, created_at FROM users WHERE username = :username LIMIT 1";
    $user = $db->fetchOne($sql, ['username' => $username]);

    // Check if user exists
    if (!$user) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'User tidak ditemukan'
        ]);
        exit();
    }

    // Verify password
    // Jika menggunakan password hash:
    // $passwordValid = password_verify($password, $user['password']);

    // Jika plaintext (untuk testing):
    $passwordValid = ($password === $user['password']);

    if (!$passwordValid) {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'Password salah'
        ]);
        exit();
    }

    // Update last login
    $db->update(
        'users',
        ['last_login' => date('Y-m-d H:i:s')],
        'id = :id',
        ['id' => $user['id']]
    );

    // Generate token
    $token = generateToken($user);

    // Success response
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Login berhasil',
        'data' => [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'email' => $user['email'] ?? '',
            'token' => $token
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error',
        'debug' => $e->getMessage() // Hapus di production
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error',
        'debug' => $e->getMessage() // Hapus di production
    ]);
}
