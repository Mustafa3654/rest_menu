<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin();

// -------------------------
// Load settings (cached)
// -------------------------
$settings = get_settings();

$message = "";

// -------------------------
// Handle item creation
// -------------------------
if (isset($_POST["submit"])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert-custom alert-custom-error'>Invalid request token. Please refresh and try again.</div>";
    } else {
        $name = trim($_POST["item-name"]);
        $price_usd = trim($_POST["price-usd"]);
        $category = trim($_POST["category"]);
        $ingredients = trim($_POST["ingredients"] ?? '');
        $price_suffix = trim($_POST["price_suffix"] ?? '');

        if ($name === '') {
            $message = "<div class='alert-custom alert-custom-error'>Item Name is required.</div>";
        } else {
            // Validate USD price
            if ($price_usd !== '' && (!is_numeric($price_usd) || $price_usd < 0)) {
                $message = "<div class='alert-custom alert-custom-error'>USD Price must be a positive number.</div>";
            } else {
                // Validate category exists
                $stmt_cat = $conn->prepare("SELECT COUNT(*) FROM categories WHERE cat_name = ?");
                $stmt_cat->bind_param("s", $category);
                $stmt_cat->execute();
                $stmt_cat->bind_result($cat_exists);
                $stmt_cat->fetch();
                $stmt_cat->close();

                if (!$cat_exists) {
                    $message = "<div class='alert-custom alert-custom-error'>Selected category does not exist.</div>";
                } else {
                    $db_path = '';
                    if (isset($_FILES["item-img"]) && $_FILES["item-img"]["error"] === 0) {
                        $img_name = $_FILES['item-img']['name'];
                        $tmp_name = $_FILES['item-img']['tmp_name'];
                        $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
                        $allowed_exs = array("jpg", "jpeg", "png", "gif", "webp");
                        
                        if (in_array($img_ex, $allowed_exs)) {
                            $target_dir = "../items/";
                            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
                            $unique_filename = uniqid("IMG-", true) . '.' . $img_ex;
                            $target_file = $target_dir . $unique_filename;
                            if (move_uploaded_file($tmp_name, $target_file)) {
                                $db_path = "items/" . $unique_filename;
                            }
                        }
                    }

                    $price_usd = $price_usd === '' ? 0 : (float)$price_usd;

                    $stmt = $conn->prepare("INSERT INTO items (item_name, item_category, Ingredients, item_priceusd, price_suffix, item_pic) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssdss", $name, $category, $ingredients, $price_usd, $price_suffix, $db_path);
                    
                    if ($stmt->execute()) {
                        $newId = $conn->insert_id;

                        $message = "<div class='alert-custom alert-custom-success'>Item Added Successfully!</div>";
                    } else {
                        $message = "<div class='alert-custom alert-custom-error'>Database Error: " . htmlspecialchars($stmt->error) . "</div>";
                    }
                    $stmt->close();
                }
            }
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
    <title>Add Item</title>
    <link rel="stylesheet" href="../style/tailwind.css">
</head>
<body class="bg-[#F7F5EA] font-poppins min-h-screen flex items-center justify-center py-10">
    <div class="bg-white p-8 rounded-xl shadow-lg w-[500px] mx-auto">
        <h1 class="text-[#42522B] text-center text-2xl font-bold mb-6">Add Item</h1>
        <?php echo $message; ?>
        <form action="addItem" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            <?php echo csrf_input(); ?>
            <label for="item-name" class="font-bold text-sm">Item Name</label>
            <input type="text" id="item-name" name="item-name" placeholder="Enter item name" required class="p-2.5 border border-gray-300 rounded-md text-sm">
            
            <label for="ingredients" class="font-bold text-sm">Item Ingredients</label>
            <textarea id="ingredients" name="ingredients" placeholder="Enter item ingredients" rows="4" class="p-2.5 border border-gray-300 rounded-md text-sm resize-y"></textarea>

            <label for="price-usd" class="font-bold text-sm">Item Price (USD)</label>
            <div class="flex gap-2">
                <input type="number" id="price-usd" name="price-usd" placeholder="0.00" step="0.10" class="p-2.5 border border-gray-300 rounded-md text-sm flex-[2]">
                <input type="text" name="price_suffix" placeholder="/lb or LG" class="p-2.5 border border-gray-300 rounded-md text-sm flex-1">
            </div>

            <label for="category" class="font-bold text-sm">Item Category</label>
            <select name="category" id="category" required class="p-2.5 border border-gray-300 rounded-md text-sm">
                <option value="" disabled selected>Select a category</option>
                <?php 
                $sql = "SELECT cat_name FROM categories ORDER BY `Order` ASC";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<option value='".htmlspecialchars($row["cat_name"])."'>".htmlspecialchars($row["cat_name"])."</option>";
                }
                ?>
            </select>

            <label for="item-img" class="font-bold text-sm">Item Image (Optional)</label>
            <input type="file" name="item-img" id="item-img" class="text-sm">

            <button type="submit" name="submit" value="submit" class="py-3 bg-[#42522B] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#2b3a1d]">Add Item</button> 
        </form>
        <a href="dashboard" class="block mt-4 no-underline"><button type="button" class="w-full py-3 bg-[#6c757d] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#5a6268]">BACK</button></a>
    </div>
</body>
</html>
