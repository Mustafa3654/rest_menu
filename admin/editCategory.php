<?php 
include "../includes/connection.php";
include "../includes/auth.php";
include "../includes/webp_helper.php";
start_secure_session();
require_admin();
check_session_timeout(30);

$message = "";

// -------------------------
// Load target category from query
// -------------------------
$row = null;
if (isset($_GET["category"])) {
    $category_name = $_GET["category"];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE cat_name = ?");
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}

// -------------------------
// Handle update action
// -------------------------
if (isset($_POST["submit"])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token. Please refresh and try again.</div>";
    } else {
        $id = $_POST['id'];
        $cat = trim($_POST["cat-name"]);
        $icon_path = $_POST['current_icon'];
        $cat_footer = trim($_POST['cat_footer'] ?? '');
        $cat_footer_bottom = trim($_POST['cat_footer_bottom'] ?? '');
        $cat_order = (int)($_POST['cat_order'] ?? 0);

        // Handle Category Icon — auto-convert to WebP
        if (isset($_FILES['cat-icon']) && $_FILES['cat-icon']['error'] === 0) {
            $result_path = process_upload_to_webp(
                $_FILES['cat-icon']['tmp_name'],
                $_FILES['cat-icon']['name'],
                '../assets/images/items/',
                'ICON',
                'assets/images/items/'
            );
            if ($result_path !== false) {
                // Delete old icon if it exists
                if (!empty($icon_path) && file_exists('../' . $icon_path)) {
                    @unlink('../' . $icon_path);
                }
                $icon_path = $result_path;
            }
        }
      
        $stmt = $conn->prepare("UPDATE categories SET cat_name = ?, cat_icon = ?, cat_footer = ?, cat_footer_bottom = ?, `Order` = ? WHERE cat_id = ?");
        $stmt->bind_param("ssssii", $cat, $icon_path, $cat_footer, $cat_footer_bottom, $cat_order, $id);
      
        if ($stmt->execute()) {

            header("Location: viewCategories");
            exit;
        } else {
            $message = "<div class='alert alert-danger'>Update Failed: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

if (!$row) {
    header("Location: viewCategories");
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
    <title>Edit Category</title>
    <link rel="stylesheet" href="../assets/css/admin_form.css">
</head>
<body>
    <div class="form-container">
        <h1>Edit Category</h1>
        <?php echo $message; ?>
        <form action="editCategory.php?category=<?php echo urlencode($row['cat_name']); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_input(); ?>
            <input type="hidden" name="id" value="<?php echo $row["cat_id"]; ?>">
            <input type="hidden" name="current_icon" value="<?php echo $row["cat_icon"]; ?>">

            <label for="item-name">Category Name</label>
            <input type="text" id="item-name" name="cat-name" value="<?php echo htmlspecialchars($row['cat_name']); ?>" required>
            
            <label for="cat-icon">Category Icon (Optional)</label>
            <input type="file" name="cat-icon" id="cat-icon">
            <?php if (!empty($row['cat_icon'])): ?>
                <img src="../<?php echo htmlspecialchars($row['cat_icon']); ?>" style="width: 50px; margin-top: 10px;" alt="Current Icon">
            <?php endif; ?>

            <label for="cat_footer">Category Footer Note (Above Items)</label>
            <textarea id="cat_footer" name="cat_footer" rows="3"><?php echo htmlspecialchars($row['cat_footer'] ?? ''); ?></textarea>

            <label for="cat_footer_bottom">Category Bottom Footer Note (Under Items)</label>
            <textarea id="cat_footer_bottom" name="cat_footer_bottom" rows="3"><?php echo htmlspecialchars($row['cat_footer_bottom'] ?? ''); ?></textarea>

            <label for="cat_order">Display Order</label>
            <input type="number" name="cat_order" id="cat_order" value="<?php echo (int)($row['Order'] ?? 0); ?>" min="0" placeholder="0 = first">

            <button type="submit" name="submit" value="submit">Update Category</button>
        </form>
        <br>
        <a href="viewCategories" class="back-link"><button type="button">BACK</button></a>
    </div>
</body>
</html>



