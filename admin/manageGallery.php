<?php
include "../includes/connection.php";
include "../includes/auth.php";
include "../includes/webp_helper.php";
start_secure_session();
require_admin();
check_session_timeout(30);

$message = "";

// Handle Upload
if (isset($_POST['upload'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token.</div>";
    } else {
        if (isset($_FILES['photos'])) {
            $total = count($_FILES['photos']['name']);
            $success_count = 0;
            $error_count = 0;

            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['photos']['error'][$i] === 0) {
                    $result_path = process_upload_to_webp(
                        $_FILES['photos']['tmp_name'][$i],
                        $_FILES['photos']['name'][$i],
                        '../assets/images/admin/pics/',
                        'VIBE',
                        'assets/images/admin/pics/'
                    );

                    if ($result_path !== false) {
                        $stmt = $conn->prepare("INSERT INTO gallery (photo_path) VALUES (?)");
                        $stmt->bind_param("s", $result_path);
                        $stmt->execute();
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }
            if ($success_count > 0) {
                $message = "<div class='alert alert-success'>$success_count photo(s) added to gallery!</div>";
                if ($error_count > 0) {
                    $message .= "<div class='alert alert-warning'>$error_count photo(s) failed to upload (invalid type or error).</div>";
                }
            } else if ($error_count > 0) {
                $message = "<div class='alert alert-danger'>Failed to upload photos. Invalid file types or errors.</div>";
            }
        }
    }
}

// Handle Delete (POST only, CSRF-protected)
if (isset($_POST['delete'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token.</div>";
    } else {
        $id = (int)$_POST['delete'];
        $stmt = $conn->prepare("SELECT photo_path FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $real_path = '../' . $row['photo_path'];
            if (file_exists($real_path)) unlink($real_path);
            $delStmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $delStmt->bind_param("i", $id);
            $delStmt->execute();
            $message = "<div class='alert alert-success'>Photo deleted.</div>";
        }
    }
}

$galleryItems = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
$csrfToken = ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery</title>
    <link rel="stylesheet" href="../assets/css/admin_form.css">
    <link rel="stylesheet" href="../assets/css/admin-shared.css">
</head>
<body>
    <div class="form-container" style="width: 800px;">
        <h1>Manage Gallery</h1>
        <?php echo $message; ?>
        
        <form action="manageGallery" method="POST" enctype="multipart/form-data">
            <?php echo csrf_input(); ?>
            <label for="photo">Upload Vibe Photo(s)</label>
            <input type="file" name="photos[]" id="photo" multiple required>
            <button type="submit" name="upload">Upload to Gallery</button>
        </form>

        <div class="gallery-grid">
            <?php while($row = $galleryItems->fetch_assoc()): ?>
                <div class="gallery-item">
                    <img src="../<?php echo htmlspecialchars($row['photo_path']); ?>" alt="Gallery Image" loading="lazy" width="150" height="110">
                    <form method="POST" action="manageGallery" style="display:inline;" onsubmit="return confirm('Delete this photo?')">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="delete" value="<?php echo (int)$row['id']; ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <br><br>
        <a href="dashboard"><button type="button" style="background:#666;">Back to Dashboard</button></a>
    </div>
</body>
</html>




