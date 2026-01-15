<?php
include "connection.php";
session_start();

if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] !== true) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $cat_id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM categories WHERE cat_id = ?");
    $stmt->bind_param("i", $cat_id);

    if ($stmt->execute()) {
        header("Location: viewCategories.php");
    } else {
        echo "Error deleting category: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}

$conn->close();
?>
