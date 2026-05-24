<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin();
check_session_timeout(30);

// -------------------------
// Read filters & pagination
// -------------------------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$csrfToken = ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Categories</title>
    <link rel="stylesheet" href="../assets/css/view.css?v=1.1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/admin-shared.css?v=1.1">
</head>
<body>
    <div class="cat-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>View Categories</h1>
            <a href="dashboard" class="back-btn">BACK</a>
        </div>
        
        <div class="controls">
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search categories by name..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>

        <?php
        $where = "";
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where .= " AND cat_name LIKE ?";
            $params[] = "%$search%";
            $types .= "s";
        }

        // Count total
        $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM categories WHERE 1=1" . $where);
        if (!empty($params)) {
            $countStmt->bind_param($types, ...$params);
        }
        $countStmt->execute();
        $totalCats = (int)$countStmt->get_result()->fetch_assoc()['total'];
        $countStmt->close();
        $totalPages = max(1, (int)ceil($totalCats / $perPage));
        if ($page > $totalPages) $page = $totalPages;
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM categories WHERE 1=1" . $where . " ORDER BY cat_name LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";

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
                        <span><a href='editCategory?category=" . urlencode($row["cat_name"]) . "'><i class='fas fa-pen'></i></a></span>
                        <span>
                            <form method='POST' action='deleteCategory' style='display:inline;' onsubmit='return confirm(\"Are you sure?\");'>
                                " . csrf_input() . "
                                <input type='hidden' name='id' value='" . (int)$row["cat_id"] . "'>
                                <button type='submit' style='background:none;border:none;padding:0;cursor:pointer;'><i class='fas fa-trash'></i></button>
                            </form>
                        </span>
                      </div>";
            }
            echo '</div>';

            // Pagination nav
            echo '<div class="pagination">';
            $qp = [];
            if ($search !== '') $qp['search'] = $search;
            if ($page > 1):
                $qp['page'] = $page - 1;
                echo '<a href="viewCategories?' . http_build_query($qp) . '" class="page-link">&laquo; Prev</a>';
            endif;
            for ($i = 1; $i <= $totalPages; $i++):
                $qp['page'] = $i;
                $active = $i === $page ? ' class="page-link active"' : ' class="page-link"';
                echo '<a href="viewCategories?' . http_build_query($qp) . '"' . $active . '>' . $i . '</a>';
            endfor;
            if ($page < $totalPages):
                $qp['page'] = $page + 1;
                echo '<a href="viewCategories?' . http_build_query($qp) . '" class="page-link">Next &raquo;</a>';
            endif;
            echo '<span class="page-info">' . $totalCats . ' total categories</span>';
            echo '</div>';
        } else {
            echo "<div class='alert alert-info' style='text-align:center; padding: 20px;'>No categories found.</div>";
        }
        $stmt->close();
        ?>
    </div>
</body>
</html>




