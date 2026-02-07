<?php
include "connection.php";
include "auth.php";
start_secure_session();
require_admin();

// Allow deletion only via POST from CSRF-protected forms.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: viewCategories.php");
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    echo "Invalid CSRF token.";
    exit;
}

$catId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$catId) {
    header("Location: viewCategories.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM categories WHERE cat_id = ?");
$stmt->bind_param("i", $catId);

if ($stmt->execute()) {
    header("Location: viewCategories.php");
} else {
    echo "Error deleting category: " . htmlspecialchars($stmt->error);
}
$stmt->close();

$conn->close();
?>
