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
        $message = "<div class='alert-custom alert-custom-error'>Invalid request token. Please refresh and try again.</div>";
    } else {
        $id = $_POST['id'];
        $cat = trim($_POST["cat-name"]);
        $icon_path = $_POST['current_icon'];
        $cat_footer = trim($_POST['cat_footer'] ?? '');

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
      
        $stmt = $conn->prepare("UPDATE categories SET cat_name = ?, cat_icon = ?, cat_footer = ? WHERE cat_id = ?");
        $stmt->bind_param("sssi", $cat, $icon_path, $cat_footer, $id);
      
        if ($stmt->execute()) {

            header("Location: viewCategories");
            exit;
        } else {
            $message = "<div class='alert-custom alert-custom-error'>Update Failed: " . htmlspecialchars($stmt->error) . "</div>";
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
    <link rel="stylesheet" href="../style/tailwind.css">
</head>
<body class="bg-[#F7F5EA] font-poppins min-h-screen flex items-center justify-center py-10">
    <div class="bg-white p-8 rounded-xl shadow-lg w-[500px] mx-auto">
        <h1 class="text-[#42522B] text-center text-2xl font-bold mb-6">Edit Category</h1>
        <?php echo $message; ?>
        <form action="editCategory.php?category=<?php echo urlencode($row['cat_name']); ?>" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            <?php echo csrf_input(); ?>
            <input type="hidden" name="id" value="<?php echo $row["cat_id"]; ?>">
            <input type="hidden" name="current_icon" value="<?php echo $row["cat_icon"]; ?>">

            <label for="item-name" class="font-bold text-sm">Category Name</label>
            <input type="text" id="item-name" name="cat-name" value="<?php echo htmlspecialchars($row['cat_name']); ?>" required class="p-2.5 border border-gray-300 rounded-md text-sm">
            
            <label for="cat-icon" class="font-bold text-sm">Category Icon (Optional)</label>
            <input type="file" name="cat-icon" id="cat-icon" class="text-sm">
            <?php if (!empty($row['cat_icon'])): ?>
                <img src="../<?php echo htmlspecialchars($row['cat_icon']); ?>" style="width: 50px; margin-top: 10px;" alt="Current Icon">
            <?php endif; ?>

            <label for="cat_footer" class="font-bold text-sm">Category Footer Note</label>
            <textarea id="cat_footer" name="cat_footer" rows="3" class="p-2.5 border border-gray-300 rounded-md text-sm resize-y"><?php echo htmlspecialchars($row['cat_footer'] ?? ''); ?></textarea>

            <button type="submit" name="submit" value="submit" class="py-3 bg-[#42522B] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#2b3a1d]">Update Category</button>
        </form>
        <a href="viewCategories" class="block mt-4 no-underline"><button type="button" class="w-full py-3 bg-[#6c757d] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#5a6268]">BACK</button></a>
    </div>
</body>
</html>
