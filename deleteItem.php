<?php
include "connection.php";
session_start();

if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] !== true) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $item_id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);

    if ($stmt->execute()) {
        header("Location: viewItems.php");
    } else {
        echo "Error deleting item: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}

$conn->close();
?>
