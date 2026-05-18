<?php 
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin();

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

        if (isset($_FILES['cat-icon']) && $_FILES['cat-icon']['error'] === 0) {
            $img_name = $_FILES['cat-icon']['name'];
            $tmp_name = $_FILES['cat-icon']['tmp_name'];
            $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
            $allowed_exs = array("jpg", "jpeg", "png", "webp");

            if (in_array($img_ex, $allowed_exs)) {
                $upload_folder = '../items/';
                if (!is_dir($upload_folder)) mkdir($upload_folder, 0755, true);
                $new_img_name = uniqid("ICON-", true).'.'.$img_ex;
                $icon_upload_path = $upload_folder . $new_img_name;
                if (move_uploaded_file($tmp_name, $icon_upload_path)) {
                    $icon_path = 'items/' . $new_img_name;
                }
            }
        }
      
        $stmt = $conn->prepare("UPDATE categories SET cat_name = ?, cat_icon = ?, cat_footer = ?, cat_footer_bottom = ? WHERE cat_id = ?");
        $stmt->bind_param("ssssi", $cat, $icon_path, $cat_footer, $cat_footer_bottom, $id);
      
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
    <link rel="stylesheet" href="../style/admin_form.css">
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

            <button type="submit" name="submit" value="submit">Update Category</button>
        </form>
        <br>
        <a href="viewCategories" class="back-link"><button type="button">BACK</button></a>
    </div>
</body>
</html>



