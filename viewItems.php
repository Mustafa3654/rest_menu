<?php
include "connection.php";
session_start();

if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] !== true) {
    header("Location: index.php");
    exit;
}

// Handle search and category filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Items</title>
    <link rel="stylesheet" href="style/view.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .controls { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 10px; display: flex; gap: 20px; align-items: center; flex-wrap: wrap; }
        .search-box { flex: 1; min-width: 200px; }
        .search-box input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .category-filter select { padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; min-width: 150px; }
        .back-btn { background: #1a2a6c; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>View Items</h1>
            <a href="dashboard.php" class="back-btn">BACK</a>
        </div>
        
        <div class="controls">
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search items by name..." value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
            </form>
            
            <div class="category-filter">
                <select name="category" onchange="filterByCategory(this.value)">
                    <option value="">All Categories</option>
                    <?php
                    $cat_sql = "SELECT DISTINCT cat_name FROM categories ORDER BY cat_name";
                    $cat_result = $conn->query($cat_sql);
                    while($cat_row = $cat_result->fetch_assoc()) {
                        $selected = ($category_filter === $cat_row['cat_name']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($cat_row['cat_name']) . "' $selected>" . htmlspecialchars($cat_row['cat_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <?php
        $sql = "SELECT * FROM items WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($search)) {
            $sql .= " AND item_name LIKE ?";
            $params[] = "%$search%";
            $types .= "s";
        }

        if (!empty($category_filter)) {
            $sql .= " AND item_category = ?";
            $params[] = $category_filter;
            $types .= "s";
        }

        $sql .= " ORDER BY item_category, item_name";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="item-list">';
            echo '<div class="item-row item-header">';
            echo '<span>Item Name</span>';
            echo '<span>Category</span>';
            echo '<span>Price (LBP)</span>';
            echo '<span>Price (USD)</span>';
            echo '<span>Edit</span>';
            echo '<span>Delete</span>';
            echo '</div>';

            while($row = $result->fetch_assoc()) {
                echo "<div class='item-row'>
                        <span>".htmlspecialchars($row["item_name"])."</span>
                        <span>".htmlspecialchars($row["item_category"])."</span>
                        <span>".number_format($row["item_pricelbp"])." LBP</span>
                        <span>$".number_format($row["item_priceusd"], 2)."</span>
                        <span><a href='editItem.php?item=".urlencode($row["item_name"])."&category=".urlencode($row["item_category"])."'><i class='fas fa-pen'></i></a></span>
                        <span><a href='deleteItem.php?id=" . $row["item_id"] . "' onclick='return confirm(\"Are you sure?\");'><i class='fas fa-trash'></i></a></span>
                      </div>";
            }
            echo '</div>';
        } else {
            echo "<div class='alert alert-info' style='text-align:center; padding: 20px;'>No items found.</div>";
        }
        $stmt->close();
        ?>
    </div>

    <script>
        function filterByCategory(category) {
            const urlParams = new URLSearchParams(window.location.search);
            if (category) urlParams.set('category', category);
            else urlParams.delete('category');
            window.location.search = urlParams.toString();
        }
    </script>
</body>
</html>
