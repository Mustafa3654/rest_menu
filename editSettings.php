<?php
include "connection.php";
include "auth.php";
start_secure_session();
require_admin();

// -------------------------
// Load current settings
// -------------------------
$settingsQuery = "SELECT * FROM settings LIMIT 1";
$settingsResult = $conn->query($settingsQuery);
$settings = $settingsResult ? $settingsResult->fetch_assoc() : null;

/**
 * Reuse the existing path unless a new file is uploaded successfully.
 */
function uploadSettingsImage(string $fieldName, string $prefix, string $currentPath): string
{
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== 0) {
        return $currentPath;
    }

    $targetDir = "bgs/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = $prefix . "_" . time() . "_" . basename($_FILES[$fieldName]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES[$fieldName]["tmp_name"], $targetFile)) {
        return $targetFile;
    }

    return $currentPath;
}

$message = "";

// -------------------------
// Handle form submission
// -------------------------
if (isset($_POST['update_settings'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token. Please refresh and try again.</div>";
    } else {
        // Basic fields
        $name = trim($_POST['restaurant_name'] ?? '');
        $email = trim($_POST['restaurant_email'] ?? '');
        $phone = trim($_POST['restaurant_phone'] ?? '');
        $address = trim($_POST['restaurant_address'] ?? '');
        $maps = trim($_POST['restaurant_maps'] ?? '');
        $desc = trim($_POST['restaurant_description'] ?? '');
        $hours = trim($_POST['opening_hours'] ?? '');
        $whatsapp = trim($_POST['whatsapp_number'] ?? '');
        $insta = trim($_POST['instagram_url'] ?? '');
        $fb = trim($_POST['facebook_url'] ?? '');
        $opening_title = trim($_POST['opening_title'] ?? '');
        $chat_id = trim($_POST['chat_id'] ?? '');
        $bot_token = trim($_POST['bot_token'] ?? '');
        $exchange_rate = $_POST['exchange_rate'] ?? 90000;
        if (!is_numeric($exchange_rate) || $exchange_rate <= 0) {
            $exchange_rate = 90000; // Default rate if invalid
        }

        // Image fields
        $logo_path = $settings['restaurant_logo'] ?? '';
        $home_bg_path = $settings['home_bg'] ?? '';
        $menu_bg_path = $settings['menu_bg'] ?? '';
        $contact_bg_path = $settings['contact_bg'] ?? '';

        $logo_path = uploadSettingsImage('restaurant_logo', 'logo', $logo_path);
        $home_bg_path = uploadSettingsImage('home_bg', 'home', $home_bg_path);
        $menu_bg_path = uploadSettingsImage('menu_bg', 'menu', $menu_bg_path);
        $contact_bg_path = uploadSettingsImage('contact_bg', 'contact', $contact_bg_path);

        // Persist settings
        if ($settings) {
            $stmt = $conn->prepare("UPDATE settings SET 
                restaurant_name = ?, 
                restaurant_logo = ?, 
                home_bg = ?,
                menu_bg = ?,
                contact_bg = ?,
                restaurant_email = ?, 
                restaurant_phone = ?, 
                restaurant_address = ?,
                restaurant_maps = ?,
                restaurant_description = ?, 
                opening_hours = ?, 
                whatsapp_number = ?, 
                instagram_url = ?, 
                facebook_url = ?,
                opening_title = ?,
                chat_id = ?,
                bot_token = ?,
                exchange_rate = ?
                WHERE id = ?");
            $stmt->bind_param("sssssssssssssssssdi", $name, $logo_path, $home_bg_path, $menu_bg_path,$contact_bg_path, $email, $phone, $address, $maps, $desc, $hours, $whatsapp, $insta, $fb, $opening_title, $chat_id, $bot_token, $exchange_rate, $settings['id']);
        } else {
            $stmt = $conn->prepare("INSERT INTO settings (restaurant_name, restaurant_logo, home_bg, menu_bg, contact_bg, restaurant_email, restaurant_phone, restaurant_address, restaurant_maps, restaurant_description, opening_hours, whatsapp_number, instagram_url, facebook_url, opening_title, chat_id, bot_token, exchange_rate) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssssssssssd", $name, $logo_path, $home_bg_path, $menu_bg_path,$contact_bg_path,$email,$phone,$address,$maps,$desc,$hours,$whatsapp,$insta,$fb,$opening_title,$chat_id,$bot_token,$exchange_rate);
        }

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Settings updated successfully!</div>";
            // Refresh settings
            $settingsResult = $conn->query($settingsQuery);
            $settings = $settingsResult->fetch_assoc();
        } else {
            $message = "<div class='alert alert-danger'>Error updating settings: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

// Keep token generation close to render stage.
$csrfToken = ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Settings</title>
    <link rel="stylesheet" href="style/admin_form.css">
    <style>
        .current-logo { margin-top: 10px; max-width: 150px; border-radius: 8px; border: 1px solid #ddd; display: block; }
        .form-group { margin-bottom: 20px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; font-weight: 600; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 20px; }
        .form-group input, .form-group textarea { width: 100%; box-sizing: border-box; }
        .submit-btn { width: 100%; margin-top: 10px; }
        .back-link button { width: 100%; margin-top: 10px;  background: #e74c3c; }
        .back-link button:hover { background: #c0392b; }
    </style>
</head> 
<body>
    <div class="form-container">
        <h1>Restaurant Settings</h1>
        <?php echo $message; ?>
        <form action="editSettings.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <div class="form-group">
                <label for="restaurant_name">Restaurant Name</label>
                <input type="text" name="restaurant_name" value="<?php echo htmlspecialchars($settings['restaurant_name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="restaurant_logo">Restaurant Logo</label>
                <input type="file" name="restaurant_logo" accept="image/*">
                <?php if (!empty($settings['restaurant_logo'])): ?>
                    <img src="<?php echo htmlspecialchars($settings['restaurant_logo']); ?>" class="current-logo" alt="Current Logo">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="home_bg">Home Page Hero Background</label>
                <input type="file" name="home_bg" accept="image/*">
                <?php if (!empty($settings['home_bg'])): ?>
                    <img src="<?php echo htmlspecialchars($settings['home_bg']); ?>" class="current-logo" alt="Home BG">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="menu_bg">Menu Page Hero Background</label>
                <input type="file" name="menu_bg" accept="image/*">
                <?php if (!empty($settings['menu_bg'])): ?>
                    <img src="<?php echo htmlspecialchars($settings['menu_bg']); ?>" class="current-logo" alt="Menu BG">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="contact_bg">Contact Page Hero Background</label>
                <input type="file" name="contact_bg" accept="image/*">
                <?php if (!empty($settings['contact_bg'])): ?>
                    <img src="<?php echo htmlspecialchars($settings['contact_bg']); ?>" class="current-logo" alt="Contact BG">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="opening_title">Opening Title (e.g., Open Daily)</label>
                <br>
                <input type="text" name="opening_title" value="<?php echo htmlspecialchars($settings['opening_title'] ?? 'Open Daily'); ?>">
            </div>

            <div class="form-group">
                <label for="opening_hours">Opening Hours</label>
                <br>
                <input type="text" name="opening_hours" value="<?php echo htmlspecialchars($settings['opening_hours'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="restaurant_phone">Phone Number</label>
                <br>
                <input type="text" name="restaurant_phone" value="<?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="whatsapp_number">WhatsApp Number</label>
                <br>
                <input type="text" name="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="restaurant_email">Email Address</label>
                <br>
                <input type="email" name="restaurant_email" value="<?php echo htmlspecialchars($settings['restaurant_email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="restaurant_address">Address</label>
                <br>
                <input type="text" name="restaurant_address" value="<?php echo htmlspecialchars($settings['restaurant_address'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="restaurant_maps">Location Map Link</label>
                <br>
                <input type="url" name="restaurant_maps" value="<?php echo htmlspecialchars($settings['restaurant_maps'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="instagram_url">Instagram URL</label>
                <br>
                <input type="url" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="facebook_url">Facebook URL</label>
                <br>
                <input type="url" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="restaurant_description">Restaurant Description</label>
                <br>
                <textarea name="restaurant_description"><?php echo htmlspecialchars($settings['restaurant_description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="chat_id">Telegram Chat ID</label>
                <br>
                <input type="text" name="chat_id" value="<?php echo htmlspecialchars($settings['chat_id'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="bot_token">Telegram Bot Token</label>
                <br>
                <input type="text" name="bot_token" value="<?php echo htmlspecialchars($settings['bot_token'] ?? ''); ?>"> 
            </div>

            <div class="form-group">
                <label for="exchange_rate">Exchange Rate (LBP per 1 USD)</label>
                <br>
                <input type="number" name="exchange_rate" value="<?php echo htmlspecialchars($settings['exchange_rate'] ?? '90000'); ?>" step="1" min="1">
                <small style="color: #666; display: block; margin-top: 5px;">e.g., 90000 means 1 USD = 90,000 LBP</small>
            </div>

            <button type="submit" name="update_settings" class="submit-btn">Save Settings</button>
            <a href="dashboard.php" class="back-link"><button type="button">BACK</button></a>
        </form>
    </div>
</body>
</html>
