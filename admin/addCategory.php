<?php 
include "../includes/connection.php";
include "../includes/auth.php";
include "../includes/webp_helper.php";
start_secure_session();
require_admin();
check_session_timeout(30);

$message = "";

// -------------------------
// Handle category creation
// -------------------------
if (isset($_POST['submit'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token. Please refresh and try again.</div>";
    } else {
        $name = trim($_POST["item-name"]);
        $db_img_path = '';
        $db_icon_path = '';
        $cat_footer = trim($_POST['cat_footer'] ?? '');
        $cat_footer_bottom = trim($_POST['cat_footer_bottom'] ?? '');
        $cat_order = (int)($_POST['cat_order'] ?? 0);

        // Handle Category Image — auto-convert to WebP
        if (isset($_FILES['item-img']) && $_FILES['item-img']['error'] === 0) {
            $result_path = process_upload_to_webp(
                $_FILES['item-img']['tmp_name'],
                $_FILES['item-img']['name'],
                '../assets/images/items/',
                'CAT',
                'assets/images/items/'
            );
            if ($result_path !== false) {
                $db_img_path = $result_path;
            }
        }

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
                $db_icon_path = $result_path;
            }
        }

        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO categories (cat_name, cat_picture, cat_icon, cat_footer, cat_footer_bottom, `Order`) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $name, $db_img_path, $db_icon_path, $cat_footer, $cat_footer_bottom, $cat_order);
            if ($stmt->execute()) {
                $newId = $conn->insert_id;

                $message = "<div class='alert alert-success'>Category Added Successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        }
    }
}

$csrfToken = ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="../assets/css/admin_form.css">
</head>
<body>
    <div class="form-container">
        <h1>Add Category</h1>
        <?php echo $message; ?>
        <form action="addCategory" method="POST" enctype="multipart/form-data">
            <?php echo csrf_input(); ?>
            <label for="item-name">Category Name</label>
            <input type="text" id="item-name" name="item-name" placeholder="Enter Category name" required>

            <label for="item-img">Category Image (Optional)</label>
            <input type="file" name="item-img" id="item-img">

            <label for="cat-icon">Category Icon (Optional)</label>
            <input type="file" name="cat-icon" id="cat-icon">

            <label for="cat_footer">Category Footer Note (Above Items)</label>
            <textarea id="cat_footer" name="cat_footer" placeholder="Enter text to display above the items" rows="3"></textarea>

            <label for="cat_footer_bottom">Category Bottom Footer Note (Under Items)</label>
            <textarea id="cat_footer_bottom" name="cat_footer_bottom" placeholder="Enter text to display under the items" rows="3"></textarea>

            <label for="cat_order">Display Order</label>
            <input type="number" name="cat_order" id="cat_order" value="0" min="0" placeholder="0 = first">

            <button type="submit" name="submit" value="submit">Add Category</button>
        </form>
        <br>
        <a href="dashboard" class="back-link"><button type="button">BACK</button></a>
    </div>
</body>
</html>



