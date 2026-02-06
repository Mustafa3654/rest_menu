<?php 
include "connection.php";
session_start();

if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] !== true) {
    header("Location: index.php");
    exit;
}

$message = "";

if (isset($_POST["submit"])) {
    $id = $_POST['id'];
    $name = trim($_POST['item-name']);
    $pricelbp = $_POST['price-lbp'];
    $priceusd = $_POST['price-usd'];
    $ingredients = $_POST["ingredients"];
    $cat = $_POST["category"];
    $pic_path = $_POST['current_pic'];

    // Handle image upload
    if (isset($_FILES['item-img']) && $_FILES['item-img']['error'] === 0) {
        $img_name = $_FILES['item-img']['name'];
        $tmp_name = $_FILES['item-img']['tmp_name'];
        $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_exs = array("jpg", "jpeg", "png", "gif", "webp");

        if (in_array($img_ex, $allowed_exs)) {
            $upload_folder = 'items/';
            if (!is_dir($upload_folder)) mkdir($upload_folder, 0777, true);
            $new_img_name = uniqid("IMG-", true).'.'.$img_ex;
            $pic_path = $upload_folder . $new_img_name;
            move_uploaded_file($tmp_name, $pic_path);
        }
    }

    $stmt = $conn->prepare("UPDATE items SET item_name=?, item_pricelbp=?, Ingredients=?, item_priceusd=?, item_category=?, item_pic=? WHERE item_id=?");
    $stmt->bind_param("sisdssi", $name, $pricelbp, $ingredients, $priceusd, $cat, $pic_path, $id);
    
    if ($stmt->execute()) {
        header("Location: viewItems.php");
        exit;
    } else {
        $message = "<div class='alert alert-danger'>Update Failed: " . htmlspecialchars($stmt->error) . "</div>";
    }
    $stmt->close();
}

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
    header("Location: viewItems.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link rel="stylesheet" href="style/add.css">
</head>
<body>
    <div class="form-container">
        <h1>Edit Item</h1>
        <?php echo $message; ?>
        <form action="editItem.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $row["item_id"]; ?>">
            <input type="hidden" name="current_pic" value="<?php echo $row["item_pic"]; ?>">

            <label for="item-name">Item Name</label>
            <input type="text" id="item-name" name="item-name" value="<?php echo htmlspecialchars($row['item_name']); ?>" required>
            
            <label for="ingredients">Item Ingredients</label>
            <textarea id="ingredients" name="ingredients" rows="4"><?php echo htmlspecialchars($row['Ingredients']); ?></textarea>

            <label for="price-lbp">Item Price (LBP)</label>
            <input type="number" id="price-lbp" name="price-lbp" value="<?php echo $row['item_pricelbp']; ?>">
            
            <label for="price-usd">Item Price (USD)</label>
            <input type="number" id="price-usd" name="price-usd" value="<?php echo $row['item_priceusd']; ?>" step="0.01">
  
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
                <img src="<?php echo htmlspecialchars($row['item_pic']); ?>" style="width: 80px; margin-top: 10px; border-radius: 8px;" alt="Current Image">
            <?php endif; ?>

            <button type="submit" name="submit" value="update">Update Item</button> 
        </form>
        <br>
        <a href="viewItems.php" class="back-link"><button type="button">BACK</button></a>
    </div>
</body>
</html>
