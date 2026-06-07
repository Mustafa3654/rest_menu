<?php
/**
 * WebP Migration Script
 * 
 * Automatically iterates through database items, categories, gallery entries, and settings
 * and converts existing JPEG/PNG/GIF files to WebP. It also updates the references in the database.
 */

include "../includes/connection.php";
include "../includes/auth.php";
include "../includes/webp_helper.php";

start_secure_session();
require_admin();
check_session_timeout(30);

// Set high execution limit and memory limit as image processing can be intensive
@set_time_limit(300);
@ini_set('memory_limit', '256M');

$logs = [];
$success_count = 0;
$fail_count = 0;
$skipped_count = 0;

function log_message($msg) {
    global $logs;
    $logs[] = $msg;
}

/**
 * Utility to process a specific file path, convert it to WebP if needed,
 * and return the new DB path.
 */
function migrate_file($relative_path, $quality = 75) {
    global $success_count, $fail_count, $skipped_count;

    if (empty($relative_path)) {
        $skipped_count++;
        return false;
    }

    $absolute_path = '../' . $relative_path;

    if (!file_exists($absolute_path)) {
        log_message("File not found: $relative_path");
        $fail_count++;
        return false;
    }

    $path_info = pathinfo($relative_path);
    $ext = strtolower($path_info['extension'] ?? '');

    if ($ext === 'webp') {
        $skipped_count++;
        return $relative_path; // Already WebP
    }

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
        log_message("Unsupported format ($ext): $relative_path");
        $skipped_count++;
        return false;
    }

    // Prepare WebP path
    $webp_relative_path = $path_info['dirname'] . '/' . $path_info['filename'] . '.webp';
    $webp_absolute_path = '../' . $webp_relative_path;

    log_message("Converting: $relative_path -> $webp_relative_path");

    $gd_enabled = function_exists('imagecreatefromjpeg');
    if ($gd_enabled) {
        if (convert_to_webp($absolute_path, $webp_absolute_path, $quality)) {
            // Delete original file since we now have a new .webp file
            @unlink($absolute_path);
            $success_count++;
            return $webp_relative_path;
        } else {
            log_message("Failed to convert: $relative_path");
            $fail_count++;
            return false;
        }
    } else {
        // GD is not enabled, skip conversion to avoid deleting the original file
        log_message("Skipped (GD extension missing): $relative_path");
        $skipped_count++;
        return $relative_path;
    }
}

// 1. Migrate Items
log_message("--- Migrating Items ---");
$res = $conn->query("SELECT item_id, item_pic FROM items WHERE item_pic != ''");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $new_path = migrate_file($row['item_pic']);
        if ($new_path !== false && $new_path !== $row['item_pic']) {
            $stmt = $conn->prepare("UPDATE items SET item_pic = ? WHERE item_id = ?");
            $stmt->bind_param("si", $new_path, $row['item_id']);
            $stmt->execute();
            $stmt->close();
            log_message("Updated item ID {$row['item_id']} path in DB.");
        }
    }
}

// 2. Migrate Categories (both picture and icon)
log_message("--- Migrating Categories ---");
$res = $conn->query("SELECT cat_id, cat_picture, cat_icon FROM categories");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $updates = [];
        $params = [];
        $types = "";

        $new_pic = migrate_file($row['cat_picture']);
        if ($new_pic !== false && $new_pic !== $row['cat_picture']) {
            $updates[] = "cat_picture = ?";
            $params[] = $new_pic;
            $types .= "s";
        }

        $new_icon = migrate_file($row['cat_icon']);
        if ($new_icon !== false && $new_icon !== $row['cat_icon']) {
            $updates[] = "cat_icon = ?";
            $params[] = $new_icon;
            $types .= "s";
        }

        if (!empty($updates)) {
            $sql = "UPDATE categories SET " . implode(", ", $updates) . " WHERE cat_id = ?";
            $params[] = $row['cat_id'];
            $types .= "i";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();
            log_message("Updated category ID {$row['cat_id']} in DB.");
        }
    }
}

// 3. Migrate Gallery
log_message("--- Migrating Gallery ---");
$res = $conn->query("SELECT id, photo_path FROM gallery");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $new_path = migrate_file($row['photo_path']);
        if ($new_path !== false && $new_path !== $row['photo_path']) {
            $stmt = $conn->prepare("UPDATE gallery SET photo_path = ? WHERE id = ?");
            $stmt->bind_param("si", $new_path, $row['id']);
            $stmt->execute();
            $stmt->close();
            log_message("Updated gallery photo ID {$row['id']} path in DB.");
        }
    }
}

// 4. Migrate Settings backgrounds and images
log_message("--- Migrating Settings ---");
$settings = get_settings();
if ($settings) {
    $fields_to_migrate = [
        'restaurant_logo',
        'home_bg',
        'menu_bg',
        'contact_bg',
        'about_image',
        'about_chef_image',
        'about_bg'
    ];

    $updates = [];
    $params = [];
    $types = "";

    foreach ($fields_to_migrate as $field) {
        if (!empty($settings[$field])) {
            $new_path = migrate_file($settings[$field]);
            if ($new_path !== false && $new_path !== $settings[$field]) {
                $updates[] = "$field = ?";
                $params[] = $new_path;
                $types .= "s";
            }
        }
    }

    if (!empty($updates)) {
        $sql = "UPDATE settings SET " . implode(", ", $updates);
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();
        log_message("Updated settings table in DB.");
    }
}

log_message("--- Migration Complete ---");
log_message("Successfully Converted: $success_count");
log_message("Failed: $fail_count");
log_message("Skipped / Already WebP: $skipped_count");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WebP Migration Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 40px 0; }
        .log-box { background-color: #212529; color: #f8f9fa; font-family: monospace; padding: 20px; border-radius: 6px; max-height: 450px; overflow-y: auto; }
    </style>
</head>
<body>
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">WebP Migration Tool Status</h3>
        </div>
        <div class="card-body">
            <div class="row text-center mb-4">
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light">
                        <h5>Converted</h5>
                        <h2 class="text-success"><?php echo $success_count; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light">
                        <h5>Failed</h5>
                        <h2 class="text-danger"><?php echo $fail_count; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light">
                        <h5>Skipped</h5>
                        <h2 class="text-secondary"><?php echo $skipped_count; ?></h2>
                    </div>
                </div>
            </div>
            
            <h5>Execution Logs</h5>
            <div class="log-box">
                <?php foreach ($logs as $log): ?>
                    <div><?php echo htmlspecialchars($log); ?></div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-4">
                <a href="editSettings" class="btn btn-primary">Back to Settings</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
