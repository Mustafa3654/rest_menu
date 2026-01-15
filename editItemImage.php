<?php
include "connection.php";
session_start();
include "header.php";

if ($_SESSION["isAdmin"] != true) {
    echo '<script>window.location.assign("index")</script>';
}

// Get the item ID from URL
if (!isset($_GET["id"])) {
    echo "<div class='container'>No item selected.</div>";
    exit;
}

$item_id = intval($_GET["id"]); // Always sanitize ID

// Fetch the current item data
$sql = "SELECT * FROM items WHERE item_id = $item_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<div class='container'>Item not found.</div>";
    exit;
}

$item = mysqli_fetch_assoc($result);
?>

<div class="container mt-3 mb-3">
    <h2>Edit Item Image</h2>

    <div class="mb-3">
        <label class="form-label">Current Image:</label><br>
        <?php if (!empty($item["item_pic"])) { ?>
            <img src="<?php echo htmlspecialchars($item["item_pic"]); ?>" alt="Current Image" style="max-width: 300px;">
        <?php } else { ?>
            <p>No image uploaded.</p>
        <?php } ?>
    </div>

    <form action="editItemImage.php?id=<?php echo $item_id; ?>" method="POST" enctype="multipart/form-data">
        <label class="form-label">Upload New Image:</label>
        <input type="file" class="form-control" name="newpic" accept="image/*" required><br>

        <input type="submit" class="btn btn-primary" value="Update Image" name="updateImage"/>
    </form>
</div>

<?php
if (isset($_POST["updateImage"])) {
    $target_dir = "items/";
    $image_name = basename($_FILES["newpic"]["name"]);
    $target_file = $target_dir . uniqid() . "_" . $image_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an actual image
    $check = getimagesize($_FILES["newpic"]["tmp_name"]);
    if ($check === false) {
        echo "<div class='container'>File is not an image.</div>";
        $uploadOk = 0;
    }

    if ($_FILES["newpic"]["size"] > 5000000) {
        echo "<div class='container'>Sorry, your file is too large.</div>";
        $uploadOk = 0;
    }

    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif", "webp"])) {
        echo "<div class='container'>Sorry, only JPG, JPEG, PNG, GIF & WEBP files are allowed.</div>";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "<div class='container'>Sorry, your file was not uploaded.</div>";
    } else {
        if (move_uploaded_file($_FILES["newpic"]["tmp_name"], $target_file)) {
            $new_image_path = mysqli_real_escape_string($conn, $target_file);

            // Optionally delete old image (if needed)
            if (!empty($item["item_pic"]) && file_exists($item["item_pic"])) {
                unlink($item["item_pic"]); // Delete old file
            }

            // Update database
            $update_sql = "UPDATE items SET item_pic = '$new_image_path' WHERE item_id = $item_id";
            if (mysqli_query($conn, $update_sql)) {
                echo '<div class="container">Image updated successfully!</div>';
                echo '<script>window.location.assign("editItemImage.php?id=' . $item_id . '")</script>';
            } else {
                echo "<div class='container'>Failed to update image: " . mysqli_error($conn) . "</div>";
            }
        } else {
            echo "<div class='container'>Sorry, there was an error uploading your file.</div>";
        }
    }
}

include "footer.php";
?>
