<?php
include "connection.php";
include "auth.php";
start_secure_session();
require_admin();

// Allow deletion only via POST from CSRF-protected forms.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: viewItems.php");
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    echo "Invalid CSRF token.";
    exit;
}

$itemId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$itemId) {
    header("Location: viewItems.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
$stmt->bind_param("i", $itemId);

if ($stmt->execute()) {
    header("Location: viewItems.php");
} else {
    echo "Error deleting item: " . htmlspecialchars($stmt->error);
}
$stmt->close();

$conn->close();
?>
