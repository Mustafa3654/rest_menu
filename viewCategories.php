<?php
include "connection.php";
session_start();

if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] !== true) {
    header("Location: index.php");
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Categories</title>
    <link rel="stylesheet" href="style/view.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .controls { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 10px; display: flex; gap: 20px; align-items: center; flex-wrap: wrap; }
        .search-box { flex: 1; min-width: 200px; }
        .search-box input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .back-btn { background: #1a2a6c; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="cat-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>View Categories</h1>
            <a href="dashboard.php" class="back-btn">BACK</a>
        </div>
        
        <div class="controls">
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search categories by name..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>

        <?php
        $sql = "SELECT * FROM categories WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($search)) {
            $sql .= " AND cat_name LIKE ?";
            $params[] = "%$search%";
            $types .= "s";
        }

        $sql .= " ORDER BY cat_name";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="item-list">';
            echo '<div class="cat-row item-header">';
            echo '<span>Category Name</span>';
            echo '<span>Edit</span>';
            echo '<span>Delete</span>';
            echo '</div>';

            while($row = $result->fetch_assoc()) {
                echo "<div class='cat-row'>
                        <span>".htmlspecialchars($row["cat_name"])."</span>
                        <span><a href='editCategory.php?category=".urlencode($row["cat_name"])."'><i class='fas fa-pen'></i></a></span>
                        <span><a href='deleteCategory.php?id=" . $row["cat_id"] . "' onclick='return confirm(\"Are you sure?\");'><i class='fas fa-trash'></i></a></span>
                      </div>";
            }
            echo '</div>';
        } else {
            echo "<div class='alert alert-info' style='text-align:center; padding: 20px;'>No categories found.</div>";
        }
        $stmt->close();
        ?>
    </div>
</body>
</html>
