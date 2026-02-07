<?php
include "connection.php";
include "auth.php";
start_secure_session();
require_admin();

// -------------------------
// Local helpers
// -------------------------
function isAllowedImageExtension(string $ext): bool
{
    $allowed = ["jpg", "jpeg", "png", "gif", "webp"];
    return in_array($ext, $allowed, true);
}

function isPathInsideDir(string $path, string $baseDir): bool
{
    $realBase = realpath($baseDir);
    $realPath = realpath($path);
    return $realBase !== false
        && $realPath !== false
        && strpos($realPath, $realBase . DIRECTORY_SEPARATOR) === 0;
}

// -------------------------
// Request bootstrap
// -------------------------
$itemId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$itemId) {
    header("Location: viewItems.php");
    exit;
}

$message = "";
$targetDir = "items/";

// -------------------------
// Update image action
// -------------------------
if (isset($_POST["updateImage"])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='container'>Invalid request token. Please try again.</div>";
    } elseif (!isset($_FILES["newpic"]) || $_FILES["newpic"]["error"] !== 0) {
        $message = "<div class='container'>Please choose an image to upload.</div>";
    } else {
        $tmpFile = $_FILES["newpic"]["tmp_name"];
        $imageName = basename($_FILES["newpic"]["name"]);
        $imageFileType = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $uploadOk = true;

        $check = @getimagesize($tmpFile);
        if ($check === false) {
            $message = "<div class='container'>File is not an image.</div>";
            $uploadOk = false;
        }

        if ($_FILES["newpic"]["size"] > 5000000) {
            $message = "<div class='container'>Sorry, your file is too large.</div>";
            $uploadOk = false;
        }

        if (!isAllowedImageExtension($imageFileType)) {
            $message = "<div class='container'>Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.</div>";
            $uploadOk = false;
        }

        if ($uploadOk) {
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $newFileName = uniqid("IMG-", true) . "." . $imageFileType;
            $targetFile = $targetDir . $newFileName;

            if (move_uploaded_file($tmpFile, $targetFile)) {
                $oldStmt = $conn->prepare("SELECT item_pic FROM items WHERE item_id = ?");
                $oldStmt->bind_param("i", $itemId);
                $oldStmt->execute();
                $oldResult = $oldStmt->get_result();
                $oldRow = $oldResult->fetch_assoc();
                $oldStmt->close();

                $updateStmt = $conn->prepare("UPDATE items SET item_pic = ? WHERE item_id = ?");
                $updateStmt->bind_param("si", $targetFile, $itemId);

                if ($updateStmt->execute()) {
                    $updateStmt->close();

                    $oldPath = $oldRow["item_pic"] ?? '';
                    // Delete old image only when it is inside items/ to avoid unsafe deletes.
                    if ($oldPath !== '' && file_exists($oldPath) && isPathInsideDir($oldPath, $targetDir)) {
                        @unlink($oldPath);
                    }

                    header("Location: editItemImage.php?id=" . $itemId);
                    exit;
                }

                $updateStmt->close();
                $message = "<div class='container'>Failed to update image in database.</div>";
            } else {
                $message = "<div class='container'>Error uploading image.</div>";
            }
        }
    }
}

// -------------------------
// Load current item
// -------------------------
$stmt = $conn->prepare("SELECT item_id, item_pic, item_name FROM items WHERE item_id = ?");
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    header("Location: viewItems.php");
    exit;
}

include "header.php";
?>

<div class="container mt-3 mb-3">
    <h2>Edit Item Image</h2>
    <?php echo $message; ?>

    <div class="mb-3">
        <label class="form-label">Current Image:</label><br>
        <?php if (!empty($item["item_pic"])) { ?>
            <img src="<?php echo htmlspecialchars($item["item_pic"]); ?>" alt="Current Image" style="max-width: 300px;">
        <?php } else { ?>
            <p>No image uploaded.</p>
        <?php } ?>
    </div>

    <form action="editItemImage.php?id=<?php echo (int)$itemId; ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_input(); ?>
        <label class="form-label">Upload New Image:</label>
        <input type="file" class="form-control" name="newpic" accept="image/*" required><br>

        <input type="submit" class="btn btn-primary" value="Update Image" name="updateImage"/>
    </form>
</div>

<?php include "footer.php"; ?>
