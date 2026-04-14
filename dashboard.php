<?php
include "connection.php";
include "auth.php";
start_secure_session();
require_admin('login.php');
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

            <a href="index.php" class="dashboard-button logout">Back</a>
        </div>
    </div>
</body>
</html>
