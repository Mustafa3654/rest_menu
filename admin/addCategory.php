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
        $message = "<div class='alert-custom alert-custom-error'>Invalid request token. Please refresh and try again.</div>";
    } else {
        $name = trim($_POST["item-name"]);
        $db_img_path = '';
        $db_icon_path = '';
        $cat_footer = trim($_POST['cat_footer'] ?? '');

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
            $stmt = $conn->prepare("INSERT INTO categories (cat_name, cat_picture, cat_icon, cat_footer) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $db_img_path, $db_icon_path, $cat_footer);
            if ($stmt->execute()) {
                $newId = $conn->insert_id;

                $message = "<div class='alert-custom alert-custom-success'>Category Added Successfully!</div>";
            } else {
                $message = "<div class='alert-custom alert-custom-error'>Error: " . htmlspecialchars($stmt->error) . "</div>";
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
    <link rel="stylesheet" href="../style/tailwind.css">
</head>
<body class="bg-[#F7F5EA] font-poppins min-h-screen flex items-center justify-center py-10">
    <div class="bg-white p-8 rounded-xl shadow-lg w-[500px] mx-auto">
        <h1 class="text-[#42522B] text-center text-2xl font-bold mb-6">Add Category</h1>
        <?php echo $message; ?>
        <form action="addCategory" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            <?php echo csrf_input(); ?>
            <label for="item-name" class="font-bold text-sm">Category Name</label>
            <input type="text" id="item-name" name="item-name" placeholder="Enter Category name" required class="p-2.5 border border-gray-300 rounded-md text-sm">

            <label for="item-img" class="font-bold text-sm">Category Image (Optional)</label>
            <input type="file" name="item-img" id="item-img" class="text-sm">

            <label for="cat-icon" class="font-bold text-sm">Category Icon (Optional)</label>
            <input type="file" name="cat-icon" id="cat-icon" class="text-sm">

            <label for="cat_footer" class="font-bold text-sm">Category Footer Note</label>
            <textarea id="cat_footer" name="cat_footer" placeholder="Enter footer text for this category" rows="3" class="p-2.5 border border-gray-300 rounded-md text-sm resize-y"></textarea>

            <button type="submit" name="submit" value="submit" class="py-3 bg-[#42522B] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#2b3a1d]">Add Category</button>
        </form>
        <a href="dashboard" class="block mt-4 no-underline"><button type="button" class="w-full py-3 bg-[#6c757d] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#5a6268]">BACK</button></a>
    </div>
</body>
</html>
