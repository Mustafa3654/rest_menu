<?php 
include "../includes/connection.php";
include "../includes/auth.php";
include "../includes/webp_helper.php";
start_secure_session();
require_admin();
check_session_timeout(30);

// -------------------------
// Load settings (cached)
// -------------------------
$settings = get_settings();

$message = "";

// -------------------------
// Handle update action
// -------------------------
if (isset($_POST["submit"])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token. Please refresh and try again.</div>";
    } else {
        $id = $_POST['id'];
        $name = trim($_POST['item-name']);
        $priceusd = $_POST['price-usd'];
        $ingredients = $_POST["ingredients"];
        $cat = $_POST["category"];
        $price_suffix = $_POST["price_suffix"] ?? '';
        $item_order = (int)($_POST["item_order"] ?? 0);
        $pic_path = $_POST['current_pic'];

        // Handle image upload — auto-convert to WebP
        if (isset($_FILES['item-img']) && $_FILES['item-img']['error'] === 0) {
            $result_path = process_upload_to_webp(
                $_FILES['item-img']['tmp_name'],
                $_FILES['item-img']['name'],
                '../assets/images/items/',
                'IMG',
                'assets/images/items/'
            );
            if ($result_path !== false) {
                // Delete old image if it exists
                if (!empty($pic_path) && file_exists('../' . $pic_path)) {
                    @unlink('../' . $pic_path);
                }
                $pic_path = $result_path;
            }
        }

        $stmt = $conn->prepare("UPDATE items SET item_name=?, Ingredients=?, item_priceusd=?, price_suffix=?, item_category=?, item_pic=?, `Order`=? WHERE item_id=?");
        $stmt->bind_param("ssdsssii", $name, $ingredients, $priceusd, $price_suffix, $cat, $pic_path, $item_order, $id);
        
        if ($stmt->execute()) {

            header("Location: viewItems");
            exit;
        } else {
            $message = "<div class='alert alert-danger'>Update Failed: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

// -------------------------
// Load target item by name/category pair
// -------------------------
$row = null;
if (isset($_GET["item"]) && isset($_GET["category"])) {
    $item_name = $_GET["item"];
    $item_cat = $_GET["category"];
    
    $stmt = $conn->prepare("SELECT * FROM items WHERE item_name=? AND item_category=?");
    $stmt->bind_param("ss", $item_name, $item_cat);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}

if (!$row) {
    header("Location: viewItems");
    exit;
}

// Generate token near render stage.
$csrfToken = ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link rel="stylesheet" href="../assets/css/admin_form.css">
</head>
<body>
    <div class="form-container">
        <h1>Edit Item</h1>
        <?php echo $message; ?>
        <form action="editItem" method="POST" enctype="multipart/form-data">
            <?php echo csrf_input(); ?>
            <input type="hidden" name="id" value="<?php echo $row["item_id"]; ?>">
            <input type="hidden" name="current_pic" value="<?php echo $row["item_pic"]; ?>">

            <label for="item-name">Item Name</label>
            <input type="text" id="item-name" name="item-name" value="<?php echo htmlspecialchars($row['item_name']); ?>" required>
            
            <label for="ingredients">Item Ingredients</label>
            <textarea id="ingredients" name="ingredients" rows="4"><?php echo htmlspecialchars($row['Ingredients']); ?></textarea>

            <label for="price-usd">Item Price (USD)</label>
            <div style="display: flex; gap: 10px;">
                <input type="number" id="price-usd" name="price-usd" value="<?php echo $row['item_priceusd']; ?>" step="0.01" style="flex: 2;">
                <input type="text" name="price_suffix" value="<?php echo htmlspecialchars($row['price_suffix'] ?? ''); ?>" placeholder="/lb or LG" style="flex: 1;">
            </div>
  
            <label for="category">Item Category</label>
            <select name="category" id="category" required>
                <?php 
                    $currentCategory = $row['item_category'];
                    $catSql = "SELECT cat_name FROM categories ORDER BY `Order` ASC";
                    $catResult = $conn->query($catSql);
                    while($catRow = $catResult->fetch_assoc()) {
                        $selected = ($catRow["cat_name"] == $currentCategory) ? "selected" : "";
                        echo "<option value='" . htmlspecialchars($catRow["cat_name"]) . "' $selected>" . htmlspecialchars($catRow["cat_name"]) . "</option>";
                    }
                ?>
            </select>

            <label for="item-img">Item Image (Optional)</label>
            <input type="file" name="item-img" id="item-img">
            <?php if (!empty($row['item_pic'])): ?>
                <img src="../<?php echo htmlspecialchars($row['item_pic']); ?>" style="width: 80px; margin-top: 10px; border-radius: 8px;" alt="Current Image">
            <?php endif; ?>

            <label for="item_order">Display Order</label>
            <input type="number" name="item_order" id="item_order" value="<?php echo (int)($row['Order'] ?? 0); ?>" min="0" placeholder="0 = first">

            <button type="submit" name="submit" value="update">Update Item</button> 
        </form>
        <br>
        <a href="viewItems" class="back-link"><button type="button">BACK</button></a>
    </div>
    
</body>
</html>



