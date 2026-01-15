<?php
include "connection.php";
session_start();

if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] !== true) {
    header("Location: index.php");
    exit;
}

$message = "";

if (isset($_POST["submit"])) {
    $name = trim($_POST["item-name"]);
    $price_lbp = trim($_POST["price-lbp"]);
    $price_usd = trim($_POST["price-usd"]);
    $category = trim($_POST["category"]);
    $ingredients = trim($_POST["ingredients"] ?? '');

    if ($name === '') {
        $message = "<div class='alert alert-danger'>Item Name is required.</div>";
    } else {
        // Validate LBP price
        if ($price_lbp !== '' && (!is_numeric($price_lbp) || $price_lbp < 0)) {
            $message = "<div class='alert alert-danger'>LBP Price must be a positive number.</div>";
        } elseif ($price_usd !== '' && (!is_numeric($price_usd) || $price_usd < 0)) {
            $message = "<div class='alert alert-danger'>USD Price must be a positive number.</div>";
        } else {
            // Validate category exists
            $stmt_cat = $conn->prepare("SELECT COUNT(*) FROM categories WHERE cat_name = ?");
            $stmt_cat->bind_param("s", $category);
            $stmt_cat->execute();
            $stmt_cat->bind_result($cat_exists);
            $stmt_cat->fetch();
            $stmt_cat->close();

            if (!$cat_exists) {
                $message = "<div class='alert alert-danger'>Selected category does not exist.</div>";
            } else {
                $target_file = '';
                if (isset($_FILES["item-img"]) && $_FILES["item-img"]["error"] === 0) {
                    $img_name = $_FILES['item-img']['name'];
                    $tmp_name = $_FILES['item-img']['tmp_name'];
                    $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
                    $allowed_exs = array("jpg", "jpeg", "png", "gif", "webp");
                    
                    if (in_array($img_ex, $allowed_exs)) {
                        $target_dir = "items/";
                        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                        $unique_filename = uniqid("IMG-", true) . '.' . $img_ex;
                        $target_file = $target_dir . $unique_filename;
                        move_uploaded_file($tmp_name, $target_file);
                    }
                }

                $price_lbp = $price_lbp === '' ? 0 : (int)$price_lbp;
                $price_usd = $price_usd === '' ? 0 : (float)$price_usd;

                $stmt = $conn->prepare("INSERT INTO items (item_name, item_category, item_pricelbp, Ingredients, item_priceusd, item_pic) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisds", $name, $category, $price_lbp, $ingredients, $price_usd, $target_file);
                
                if ($stmt->execute()) {
                    $message = "<div class='alert alert-success'>Item Added Successfully!</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($stmt->error) . "</div>";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="style/admin_form.css">
</head>
<body>
    <div class="form-container">
        <h1>Add Item</h1>
        <?php echo $message; ?>
        <form action="addItem.php" method="POST" enctype="multipart/form-data">
            <label for="item-name">Item Name</label>
            <input type="text" id="item-name" name="item-name" placeholder="Enter item name" required>
            
            <label for="ingredients">Item Ingredients</label>
            <textarea id="ingredients" name="ingredients" placeholder="Enter item ingredients" rows="4"></textarea>

            <label for="price-lbp">Item Price (LBP)</label>
            <input type="number" id="price-lbp" name="price-lbp" placeholder="Enter Price in LBP">
            
            <label for="price-usd">Item Price (USD)</label>
            <input type="number" id="price-usd" name="price-usd" placeholder="Enter Price in USD" step="0.01">

            <label for="category">Item Category</label>
            <select name="category" id="category" required>
                <option value="" disabled selected>Select a category</option>
                <?php 
                $sql = "SELECT cat_name FROM categories ORDER BY `Order` ASC";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<option value='".htmlspecialchars($row["cat_name"])."'>".htmlspecialchars($row["cat_name"])."</option>";
                }
                ?>
            </select>

            <label for="item-img">Item Image (Optional)</label>
            <input type="file" name="item-img" id="item-img">

            <button type="submit" name="submit" value="submit">Add Item</button> 
        </form>
        <br>
        <a href="dashboard.php" class="back-link"><button type="button">BACK</button></a>
    </div>
</body>
</html>
