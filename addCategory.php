<?php 
include "connection.php";
include "auth.php";
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
        $img_upload_path = '';
        $icon_upload_path = '';

        // Handle Category Image
        if (isset($_FILES['item-img']) && $_FILES['item-img']['error'] === 0) {
            $img_name = $_FILES['item-img']['name'];
            $tmp_name = $_FILES['item-img']['tmp_name'];
            $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
            $allowed_exs = array("jpg", "jpeg", "png", "webp");

            if (in_array($img_ex, $allowed_exs)) {
                $upload_folder = 'items/';
                if (!is_dir($upload_folder)) mkdir($upload_folder, 0777, true);
                $new_img_name = uniqid("CAT-", true).'.'.$img_ex;
                $img_upload_path = $upload_folder . $new_img_name;
                move_uploaded_file($tmp_name, $img_upload_path);
            }
        }

        // Handle Category Icon
        if (isset($_FILES['cat-icon']) && $_FILES['cat-icon']['error'] === 0) {
            $img_name = $_FILES['cat-icon']['name'];
            $tmp_name = $_FILES['cat-icon']['tmp_name'];
            $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
            $allowed_exs = array("jpg", "jpeg", "png", "webp");

            if (in_array($img_ex, $allowed_exs)) {
                $upload_folder = 'items/';
                $new_img_name = uniqid("ICON-", true).'.'.$img_ex;
                $icon_upload_path = $upload_folder . $new_img_name;
                move_uploaded_file($tmp_name, $icon_upload_path);
            }
        }

        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO categories (cat_name, cat_picture, cat_icon) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $img_upload_path, $icon_upload_path);
            if ($stmt->execute()) {
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
    <link rel="stylesheet" href="style/admin_form.css">
</head>
<body>
    <div class="form-container">
        <h1>Add Category</h1>
        <?php echo $message; ?>
        <form action="addCategory.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <label for="item-name">Category Name</label>
            <input type="text" id="item-name" name="item-name" placeholder="Enter Category name" required>

            <label for="item-img">Category Image (Optional)</label>
            <input type="file" name="item-img" id="item-img">

            <label for="cat-icon">Category Icon (Optional)</label>
            <input type="file" name="cat-icon" id="cat-icon">

            <button type="submit" name="submit" value="submit">Add Category</button>
        </form>
        <br>
        <a href="dashboard.php" class="back-link"><button type="button">BACK</button></a>
    </div>
</body>
</html>
