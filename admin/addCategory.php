<?php 
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin();

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

        // Handle Category Image
        if (isset($_FILES['item-img']) && $_FILES['item-img']['error'] === 0) {
            $img_name = $_FILES['item-img']['name'];
            $tmp_name = $_FILES['item-img']['tmp_name'];
            $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
            $allowed_exs = array("jpg", "jpeg", "png", "webp");

            if (in_array($img_ex, $allowed_exs)) {
                $upload_folder = '../items/';
                if (!is_dir($upload_folder)) mkdir($upload_folder, 0755, true);
                $new_img_name = uniqid("CAT-", true).'.'.$img_ex;
                $img_upload_path = $upload_folder . $new_img_name;
                if (move_uploaded_file($tmp_name, $img_upload_path)) {
                    $db_img_path = 'items/' . $new_img_name;
                }
            }
        }

        // Handle Category Icon
        if (isset($_FILES['cat-icon']) && $_FILES['cat-icon']['error'] === 0) {
            $img_name = $_FILES['cat-icon']['name'];
            $tmp_name = $_FILES['cat-icon']['tmp_name'];
            $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
            $allowed_exs = array("jpg", "jpeg", "png", "webp");

            if (in_array($img_ex, $allowed_exs)) {
                $upload_folder = '../items/';
                $new_img_name = uniqid("ICON-", true).'.'.$img_ex;
                $icon_upload_path = $upload_folder . $new_img_name;
                if (move_uploaded_file($tmp_name, $icon_upload_path)) {
                    $db_icon_path = 'items/' . $new_img_name;
                }
            }
        }

        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO categories (cat_name, cat_picture, cat_icon, cat_footer, cat_footer_bottom) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $db_img_path, $db_icon_path, $cat_footer, $cat_footer_bottom);
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
    <link rel="stylesheet" href="../style/admin_form.css">
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

            <button type="submit" name="submit" value="submit">Add Category</button>
        </form>
        <br>
        <a href="dashboard" class="back-link"><button type="button">BACK</button></a>
    </div>
</body>
</html>



