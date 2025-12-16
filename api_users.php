<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();

require_once 'config/database.php';

$db = Database::getInstance()->getConnection();

/* =========================
   SIMPLE JWT CHECK (DEMO)
========================= */
function checkAuth()
{
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
}

/* =========================
   RATE LIMIT (SIMPLE)
========================= */
if (!isset($_SESSION['rate_limit'])) {
    $_SESSION['rate_limit'] = ['count' => 0, 'time' => time()];
}
if (time() - $_SESSION['rate_limit']['time'] < 60) {
    $_SESSION['rate_limit']['count']++;
    if ($_SESSION['rate_limit']['count'] > 30) {
        http_response_code(429);
        echo json_encode(['status' => 'error', 'message' => 'Too many requests']);
        exit;
    }
} else {
    $_SESSION['rate_limit'] = ['count' => 1, 'time' => time()];
}

checkAuth();

/* =========================
   INPUT HANDLER
========================= */
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) $input = [];

$method = $_SERVER['REQUEST_METHOD'];

try {

    /* =========================
       CREATE USER (POST)
    ========================= */
    if ($method === 'POST') {

        $username = trim($input['username'] ?? '');
        $email    = trim($input['email'] ?? '');
        $password = trim($input['password'] ?? '');
        $role     = trim($input['role'] ?? 'user');

        if ($username === '' || $email === '' || $password === '') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
            exit;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO users (username,email,password,role)
            VALUES (?,?,?,?)
        ");
        $stmt->execute([$username, $email, $hashed, $role]);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'User created',
            'data' => [
                'user' => [
                    'id' => $db->lastInsertId(),
                    'username' => $username,
                    'email' => $email,
                    'role' => $role
                ]
            ]
        ]);
        exit;
    }

    /* =========================
       READ USER(S) (GET)
    ========================= */
    if ($method === 'GET') {

        if (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT id,username,email,role,status FROM users WHERE id=?");
            $stmt->execute([$_GET['id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'success', 'data' => $user]);
            exit;
        }

        $stmt = $db->query("SELECT id,username,email,role,status FROM users");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    /* =========================
       UPDATE USER (PUT)
    ========================= */
    if ($method === 'PUT') {

        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID required']);
            exit;
        }

        $fields = [];
        $values = [];

        foreach (['username', 'email', 'role', 'status'] as $f) {
            if (isset($input[$f])) {
                $fields[] = "$f=?";
                $values[] = trim($input[$f]);
            }
        }

        if (isset($input['password'])) {
            $fields[] = "password=?";
            $values[] = password_hash($input['password'], PASSWORD_DEFAULT);
        }

        if (!$fields) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No data to update']);
            exit;
        }

        $values[] = $_GET['id'];

        $stmt = $db->prepare("UPDATE users SET " . implode(',', $fields) . " WHERE id=?");
        $stmt->execute($values);

        echo json_encode(['status' => 'success', 'message' => 'User updated']);
        exit;
    }

    /* =========================
       DELETE USER (DELETE)
    ========================= */
    if ($method === 'DELETE') {

        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID required']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$_GET['id']]);

        echo json_encode(['status' => 'success', 'message' => 'User deleted']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error',
        'detail' => $e->getMessage()
    ]);
}
