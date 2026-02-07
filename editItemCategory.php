<?php
include "connection.php";
include "auth.php";
start_secure_session();
require_admin();

// -------------------------
// Request bootstrap
// -------------------------
$message = "";
$itemName = trim($_GET["item"] ?? '');

if ($itemName === '') {
    header("Location: viewItems.php");
    exit;
}

$stmt = $conn->prepare("SELECT item_id, item_name, item_category FROM items WHERE item_name = ? LIMIT 1");
$stmt->bind_param("s", $itemName);
$stmt->execute();
$result = $stmt->get_result();
$itemRow = $result->fetch_assoc();
$stmt->close();

if (!$itemRow) {
    header("Location: viewItems.php");
    exit;
}

// -------------------------
// Update category action
// -------------------------
if (isset($_POST["submit"])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token. Please try again.</div>";
    } else {
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        $category = trim($_POST['categories'] ?? '');

        if (!$id || $category === '') {
            $message = "<div class='alert alert-danger'>Invalid item/category.</div>";
        } else {
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM categories WHERE cat_name = ?");
            $checkStmt->bind_param("s", $category);
            $checkStmt->execute();
            $checkStmt->bind_result($catExists);
            $checkStmt->fetch();
            $checkStmt->close();

            if ((int)$catExists !== 1) {
                $message = "<div class='alert alert-danger'>Selected category does not exist.</div>";
            } else {
                $updateStmt = $conn->prepare("UPDATE items SET item_category = ? WHERE item_id = ?");
                $updateStmt->bind_param("si", $category, $id);

                if ($updateStmt->execute()) {
                    $updateStmt->close();
                    header("Location: viewItems.php");
                    exit;
                }

                $message = "<div class='alert alert-danger'>Failed to update item category.</div>";
                $updateStmt->close();
            }
        }
    }
}

// -------------------------
// Load category list for dropdown
// -------------------------
$catResult = $conn->query("SELECT cat_name FROM categories ORDER BY `Order` ASC");
include "header.php";
?>

<div class="container">
    <h2>Edit Item Category</h2>
    <?php echo $message; ?>
    <form method="POST" action="editItemCategory.php?item=<?php echo urlencode($itemName); ?>">
        <?php echo csrf_input(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$itemRow["item_id"]; ?>">

        <select class="form-control" name="categories" id="categories" required>
            <?php
            while ($catRow = $catResult->fetch_assoc()) {
                $selected = ($catRow["cat_name"] === $itemRow["item_category"]) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($catRow["cat_name"]) . "' $selected>" . htmlspecialchars($catRow["cat_name"]) . "</option>";
            }
            ?>
        </select><br>

        <input class="btn btn-primary" type="submit" value="Update" name="submit">
    </form>
</div>

<?php include "footer.php"; ?>
