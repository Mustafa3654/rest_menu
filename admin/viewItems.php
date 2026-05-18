<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin();


// -------------------------
// Read filters
// -------------------------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';

// -------------------------
// Load settings (cached)
// -------------------------
$settings = get_settings();
$csrfToken = ensure_csrf_token();

// -------------------------
// Import status messages
// -------------------------
$import_status = $_GET['import_status'] ?? '';
$import_message = isset($_GET['import_message']) ? htmlspecialchars(urldecode($_GET['import_message'])) : '';
$import_errors = $_SESSION['import_errors'] ?? [];
unset($_SESSION['import_errors']); // Clear errors after display

// -------------------------
// Load category filter options
// -------------------------
$categories = [];
$catResult = $conn->query("SELECT DISTINCT cat_name FROM categories ORDER BY cat_name");
if ($catResult) {
    while ($catRow = $catResult->fetch_assoc()) {
        $categories[] = $catRow;
    }
}

// -------------------------
// Load filtered items
// -------------------------
$sql = "SELECT * FROM items WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $sql .= " AND item_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if ($category_filter !== '') {
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
$itemRows = [];
while ($row = $result->fetch_assoc()) {
    $itemRows[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Items</title>
    <link rel="stylesheet" href="../style/view.css?v=1.1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../style/admin-shared.css?v=1.1">
</head>
<body>
    <div class="dashboard-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>View Items</h1>
            <a href="dashboard" class="back-btn">BACK</a>
        </div>
        
        <div class="controls">
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search items by name..." value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
            </form>
            
            <div class="category-filter">
                <select name="category" onchange="filterByCategory(this.value)">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat_row): ?>
                        <?php $selected = ($category_filter === $cat_row['cat_name']) ? 'selected' : ''; ?>
                        <option value="<?php echo htmlspecialchars($cat_row['cat_name']); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($cat_row['cat_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Import/Export Section -->
        <div class="import-export-section">
            <h3><i class="fas fa-file-csv"></i> Import/Export Items</h3>
            <div class="import-export-buttons">
                <a href="exportItems" class="export-btn"><i class="fas fa-download"></i> Export to CSV</a>
                <form action="importItems" method="POST" enctype="multipart/form-data" class="import-form">
                    <?php echo csrf_input(); ?>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv,text/csv" required>
                    <button type="submit" name="import" class="import-btn"><i class="fas fa-upload"></i> Import from CSV</button>
                </form>
            </div>
        </div>


        <!-- Import Status Messages -->
        <?php if ($import_status === 'success'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Import successful! <?php echo $import_message; ?>
            </div>
        <?php elseif ($import_status === 'error'): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $import_message; ?>
                <?php if (!empty($import_errors)): ?>
                    <ul style="margin-top: 10px; margin-bottom: 0;">
                        <?php foreach ($import_errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (count($itemRows) > 0): ?>
            <div class="item-list">
                <div class="item-row item-header">
                    <span>Item Name</span>
                    <span>Category</span>
                    <span class="price-usd-header">Price (USD)</span>
                    <span>Edit</span>
                    <span>Delete</span>
                </div>

                <?php foreach ($itemRows as $row): ?>
                    <?php
                    $usd_price = $row["item_priceusd"] > 0 ? "$" . number_format($row["item_priceusd"], 2) : "-";
                    if (!empty($row['price_suffix'])) {
                        $usd_price .= " " . $row['price_suffix'];
                    }
                    ?>
                    <div class='item-row' data-usd-price="<?php echo htmlspecialchars($usd_price); ?>">
                        <span><?php echo htmlspecialchars($row["item_name"]); ?></span>
                        <span><?php echo htmlspecialchars($row["item_category"]); ?></span>
                        <span class='price-usd'><?php echo $usd_price; ?></span>
                        <span><a href='editItem?item=<?php echo urlencode($row["item_name"]); ?>&category=<?php echo urlencode($row["item_category"]); ?>'><i class='fas fa-pen'></i></a></span>
                        <span>
                            <form method='POST' action='deleteItem' style='display:inline;' onsubmit='return confirm("Are you sure?");'>
                                <?php echo csrf_input(); ?>
                                <input type='hidden' name='id' value='<?php echo (int)$row["item_id"]; ?>'>
                                <button type='submit' style='background:none;border:none;padding:0;cursor:pointer;'><i class='fas fa-trash'></i></button>
                            </form>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class='alert alert-info' style='text-align:center; padding: 20px;'>No items found.</div>
        <?php endif; ?>
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




