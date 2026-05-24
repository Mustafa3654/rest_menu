<?php
require_once __DIR__ . '/includes/connection.php';
start_secure_session();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'POST required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

$customer_name = trim($input['customer_name'] ?? '');
$customer_phone = trim($input['customer_phone'] ?? '');
$items = $input['items'] ?? [];
$total_usd = (float)($input['total_usd'] ?? 0);

if (empty($customer_name) || empty($customer_phone) || empty($items)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO orders (customer_name, whatsapp_number, total_usd, status) VALUES (?, ?, ?, 'pending')");
$stmt->bind_param("ssd", $customer_name, $customer_phone, $total_usd);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'order_id' => $stmt->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
$stmt->close();
