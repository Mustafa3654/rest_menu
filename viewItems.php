<?php
include "connection.php";
include "auth.php";
start_secure_session();
require_admin();

// -------------------------
// Handle currency-display update
// -------------------------
if (isset($_POST['display_currency'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit("Invalid request token.");
    }

    $display_currency = $_POST['display_currency'];
    $allowedCurrencies = ['LBP', 'USD', 'BOTH'];
    if (in_array($display_currency, $allowedCurrencies, true)) {
        $stmt = $conn->prepare("UPDATE settings SET display_currency = ? WHERE id = 1");
        $stmt->bind_param("s", $display_currency);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: viewItems.php?" . http_build_query($_GET));
    exit;
}

// -------------------------
// Read filters
// -------------------------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';

// -------------------------
// Load settings
// -------------------------
$settingsQuery = "SELECT exchange_rate, display_currency FROM settings LIMIT 1";
$settingsResult = $conn->query($settingsQuery);
$settings = $settingsResult ? $settingsResult->fetch_assoc() : null;
$exchange_rate = $settings['exchange_rate'] ?? 90000;
$display_currency = $settings['display_currency'] ?? 'LBP';
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
    <link rel="stylesheet" href="style/view.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .controls { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 10px; display: flex; gap: 20px; align-items: center; flex-wrap: wrap; }
        .search-box { flex: 1; min-width: 200px; }
        .search-box input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .category-filter select { padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; min-width: 150px; }
        .back-btn { background: #1a2a6c; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: 600; }
        .exchange-rate-info-box { font-size: 13px; color: #555; background: #e9ecef; padding: 8px 14px; border-radius: 5px; }
        .currency-control { background: #fff3cd; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #ffc107; }
        .currency-control h3 { margin: 0 0 12px 0; font-size: 14px; color: #856404; }
        .currency-control .btn-group { display: flex; gap: 10px; flex-wrap: wrap; }
        .currency-control button { padding: 8px 18px; border: 2px solid #1a2a6c; background: white; color: #1a2a6c; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
        .currency-control button.active { background: #1a2a6c; color: white; }
        .currency-control button:hover { background: #1a2a6c; color: white; }
        
        /* Import/Export Section */
        .import-export-section { background: #e7f3ff; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a90e2; }
        .import-export-section h3 { margin: 0 0 12px 0; font-size: 14px; color: #1a5490; }
        .import-export-buttons { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
        .export-btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: 600; display: inline-block; }
        .export-btn:hover { background: #218838; }
        .import-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .import-form input[type="file"] { padding: 8px; border: 1px solid #ddd; border-radius: 5px; background: white; }
        .import-btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; }
        .import-btn:hover { background: #0056b3; }
        
        /* Alert Messages */
        .alert { padding: 15px 20px; margin-bottom: 20px; border-radius: 5px; font-weight: 500; }
        .alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .alert-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .alert-info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
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
                    <?php foreach ($categories as $cat_row): ?>
                        <?php $selected = ($category_filter === $cat_row['cat_name']) ? 'selected' : ''; ?>
                        <option value="<?php echo htmlspecialchars($cat_row['cat_name']); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($cat_row['cat_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="exchange-rate-info-box">
                <span class="exchange-rate-info">Exchange Rate: <?php echo number_format($exchange_rate); ?> LBP/USD</span>
            </div>
        </div>

        <!-- Import/Export Section -->
        <div class="import-export-section">
            <h3><i class="fas fa-file-csv"></i> Import/Export Items</h3>
            <div class="import-export-buttons">
                <a href="exportItems.php" class="export-btn"><i class="fas fa-download"></i> Export to CSV</a>
                <form action="importItems.php" method="POST" enctype="multipart/form-data" class="import-form">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="file" name="csv_file" id="csv_file" accept=".csv,text/csv" required>
                    <button type="submit" name="import" class="import-btn"><i class="fas fa-upload"></i> Import from CSV</button>
                </form>
            </div>
        </div>

        <!-- Menu Price Display Control -->
        <div class="currency-control">
            <h3><i class="fas fa-eye"></i> Menu Price Display (controls what customers see on Menu)</h3>
            <div class="btn-group">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="display_currency" value="LBP">
                    <button type="submit" class="<?php echo $display_currency === 'LBP' ? 'active' : ''; ?>">LBP Only</button>
                </form>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="display_currency" value="USD">
                    <button type="submit" class="<?php echo $display_currency === 'USD' ? 'active' : ''; ?>">USD Only</button>
                </form>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="display_currency" value="BOTH">
                    <button type="submit" class="<?php echo $display_currency === 'BOTH' ? 'active' : ''; ?>">Both Prices</button>
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
                    <span class="price-lbp-header">Price (LBP)</span>
                    <span class="price-usd-header">Price (USD)</span>
                    <span>Edit</span>
                    <span>Delete</span>
                </div>

                <?php foreach ($itemRows as $row): ?>
                    <?php
                    $lbp_price = $row["item_pricelbp"] > 0 ? number_format($row["item_pricelbp"]) . " LBP" : "-";
                    $usd_price = $row["item_priceusd"] > 0 ? "$" . number_format($row["item_priceusd"], 2) : "-";
                    ?>
                    <div class='item-row' data-lbp-price="<?php echo htmlspecialchars($lbp_price); ?>" data-usd-price="<?php echo htmlspecialchars($usd_price); ?>">
                        <span><?php echo htmlspecialchars($row["item_name"]); ?></span>
                        <span><?php echo htmlspecialchars($row["item_category"]); ?></span>
                        <span class='price-lbp'><?php echo $lbp_price; ?></span>
                        <span class='price-usd'><?php echo $usd_price; ?></span>
                        <span><a href='editItem.php?item=<?php echo urlencode($row["item_name"]); ?>&category=<?php echo urlencode($row["item_category"]); ?>'><i class='fas fa-pen'></i></a></span>
                        <span>
                            <form method='POST' action='deleteItem.php' style='display:inline;' onsubmit='return confirm("Are you sure?");'>
                                <input type='hidden' name='csrf_token' value='<?php echo htmlspecialchars($csrfToken); ?>'>
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
