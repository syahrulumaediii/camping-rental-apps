<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// ===============================
// Database Connection
// ===============================
$host = "localhost";
$user = "root";     // sesuaikan
$pass = "";         // sesuaikan
$db   = "db_camping_rental";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]));
}

// ===============================
// Query Base
// ===============================
$query = "SELECT id, name, description, category, price_per_day, quantity_available, status FROM items WHERE 1=1";

// ===============================
// Filtering jika ada (optional)
// ===============================
if (isset($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $query .= " AND category = '$category'";
}

if (isset($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $query .= " AND status = '$status'";
}

// ===============================
// Pagination (optional)
// ===============================
$limit  = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

$query .= " LIMIT $limit OFFSET $offset";

$result = $conn->query($query);

// ===============================
// Build Response
// ===============================
$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$response = [
    "status" => "success",
    "total"  => count($items),
    "data"   => $items
];

echo json_encode($response, JSON_PRETTY_PRINT);

$conn->close();
