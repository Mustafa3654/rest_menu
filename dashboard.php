<?php 
include "connection.php";
session_start();

if (!isset($_SESSION["user_name"])) {
    header("Location: login.php");
    exit();
}

$isAdmin = 0;
$username = $_SESSION["user_name"];

$stmt = $conn->prepare("SELECT isAdmin FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    $row = $res->fetch_assoc();
    if($row["isAdmin"] == 1){
        $isAdmin = 1;
    }
}
$stmt->close();

if($isAdmin == 0){
    echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>";
    echo "<h2>Access Denied.</h2>";
    echo "<p>Redirecting to login page in 3 seconds...</p>";
    echo "</div>";
    echo '<script>
        setTimeout(function() {
            window.location.assign("login.php");
        }, 3000);
    </script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style/dashboard.css">
</head>
<body>
    <div class="dashboard-container"> 
        <h1>Dashboard</h1>
        <div class="button-grid">
            <a href="addItem.php" class="dashboard-button">Add Item</a>
            <a href="addCategory.php" class="dashboard-button">Add Category</a>
            <a href="viewItems.php" class="dashboard-button">View Items</a>
            <a href="viewCategories.php" class="dashboard-button">View Categories</a>
            <a href="editSettings.php" class="dashboard-button">Settings</a>
            <a href="index.php" class="dashboard-button">Back</a>
            <a href="logout.php" class="dashboard-button logout">Logout</a>
        </div>
    </div>
</body>
</html>
