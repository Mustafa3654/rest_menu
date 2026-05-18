<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin();

// -------------------------
// Load current settings (cached)
// -------------------------
$settings = get_settings();

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
        mkdir($targetDir, 0755, true);
    }

    $fileName = $prefix . "_" . time() . "_" . basename($_FILES[$fieldName]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES[$fieldName]["tmp_name"], $targetFile)) {
        return "admin/bgs/" . $fileName;
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

        $phone = trim($_POST['restaurant_phone'] ?? '');
        $address = trim($_POST['restaurant_address'] ?? '');
        $maps = trim($_POST['restaurant_maps'] ?? '');
        $desc = trim($_POST['restaurant_description'] ?? '');
        $hours = trim($_POST['opening_hours'] ?? '');
        $whatsapp = trim($_POST['whatsapp_number'] ?? '');
        $insta = trim($_POST['instagram_url'] ?? '');
        $fb = trim($_POST['facebook_url'] ?? '');
        $opening_title = trim($_POST['opening_title'] ?? '');
        $email = trim($_POST['restaurant_email'] ?? '');
        $country_code = trim($_POST['country_code'] ?? '+1');
        $order_method = trim($_POST['order_method'] ?? 'whatsapp');

        // Banners
        $banner1_t1 = trim($_POST['banner1_t1'] ?? 'THANK YOU FOR SUPPORTING LOCAL');
        $banner1_t2 = trim($_POST['banner1_t2'] ?? 'Made with fresh ingredients & lots of love');
        $banner1_t3 = trim($_POST['banner1_t3'] ?? 'AUTHENTIC MEDITERRANEAN FLAVOR');

        $banner2_t1 = trim($_POST['banner2_t1'] ?? 'FRESH INGREDIENTS');
        $banner2_t2 = trim($_POST['banner2_t2'] ?? 'MADE DAILY');
        $banner2_t3 = trim($_POST['banner2_t3'] ?? 'AUTHENTIC RECIPES');
        $banner2_t4 = trim($_POST['banner2_t4'] ?? 'MADE WITH LOVE');


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
                restaurant_phone = ?, 
                restaurant_email = ?,
                restaurant_address = ?,
                restaurant_maps = ?,
                restaurant_description = ?, 
                opening_hours = ?, 
                whatsapp_number = ?, 
                instagram_url = ?, 
                facebook_url = ?,
                opening_title = ?,
                country_code = ?,
                order_method = ?,
                banner1_t1 = ?,
                banner1_t2 = ?,
                banner1_t3 = ?,
                banner2_t1 = ?,
                banner2_t2 = ?,
                banner2_t3 = ?,
                banner2_t4 = ?
                WHERE id = ?");
            $stmt->bind_param("ssssssssssssssssssssssssi", 
                $name, $logo_path, $home_bg_path, $menu_bg_path, $contact_bg_path, 
                $phone, $email, $address, $maps, $desc, $hours, $whatsapp, $insta, $fb, 
                $opening_title, $country_code, $order_method,
                $banner1_t1, $banner1_t2, $banner1_t3,
                $banner2_t1, $banner2_t2, $banner2_t3, $banner2_t4,
                $settings['id']
            );
        } else {
            $stmt = $conn->prepare("INSERT INTO settings (restaurant_name, restaurant_logo, home_bg, menu_bg, contact_bg, restaurant_phone, restaurant_email, restaurant_address, restaurant_maps, restaurant_description, opening_hours, whatsapp_number, instagram_url, facebook_url, opening_title, country_code, order_method, banner1_t1, banner1_t2, banner1_t3, banner2_t1, banner2_t2, banner2_t3, banner2_t4) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssssssssssssssss", 
                $name, $logo_path, $home_bg_path, $menu_bg_path, $contact_bg_path, 
                $phone, $email, $address, $maps, $desc, $hours, $whatsapp, $insta, $fb, 
                $opening_title, $country_code, $order_method,
                $banner1_t1, $banner1_t2, $banner1_t3,
                $banner2_t1, $banner2_t2, $banner2_t3, $banner2_t4
            );
        }

        if ($stmt->execute()) {
            invalidate_settings_cache();

            $message = "<div class='alert alert-success'>Settings updated successfully!</div>";
            // Refresh settings cache
            $settings = get_settings();
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
    <link rel="stylesheet" href="../style/admin_form.css">
    <link rel="stylesheet" href="../style/admin-shared.css">
</head> 
<body>
    <div class="form-container">
        <h1>Restaurant Settings</h1>
        <div style="margin-bottom: 20px; text-align: center;">
            <a href="manageGallery" style="text-decoration: none;">
                <button type="button" style="background:#42522B; color: white; width: 100%; margin-bottom: 10px;">MANAGE GALLERY PHOTOS</button>
            </a>
        </div>
        <?php echo $message; ?>
        <form action="editSettings" method="POST" enctype="multipart/form-data">
            <?php echo csrf_input(); ?>


            <div class="form-group">
                <label for="restaurant_name">Restaurant Name</label>
                <input type="text" name="restaurant_name" value="<?php echo htmlspecialchars($settings['restaurant_name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="restaurant_logo">Restaurant Logo</label>
                <input type="file" name="restaurant_logo" accept="image/*">
                <?php if (!empty($settings['restaurant_logo'])): ?>
                    <img src="../<?php echo htmlspecialchars($settings['restaurant_logo']); ?>" class="current-logo" alt="Current Logo">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="home_bg">Home Page Hero Background</label>
                <input type="file" name="home_bg" accept="image/*">
                <?php if (!empty($settings['home_bg'])): ?>
                    <img src="../<?php echo htmlspecialchars($settings['home_bg']); ?>" class="current-logo" alt="Home BG">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="menu_bg">Menu Page Hero Background</label>
                <input type="file" name="menu_bg" accept="image/*">
                <?php if (!empty($settings['menu_bg'])): ?>
                    <img src="../<?php echo htmlspecialchars($settings['menu_bg']); ?>" class="current-logo" alt="Menu BG">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="contact_bg">Contact Page Hero Background</label>
                <input type="file" name="contact_bg" accept="image/*">
                <?php if (!empty($settings['contact_bg'])): ?>
                    <img src="../<?php echo htmlspecialchars($settings['contact_bg']); ?>" class="current-logo" alt="Contact BG">
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
                <label for="restaurant_email">Email Address</label>
                <br>
                <input type="email" name="restaurant_email" value="<?php echo htmlspecialchars($settings['restaurant_email'] ?? ''); ?>">
            </div>

            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 0 0 100px;">
                    <label for="country_code">Country Code</label>
                    <input type="text" name="country_code" value="<?php echo htmlspecialchars($settings['country_code'] ?? '+1'); ?>" placeholder="+1">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="whatsapp_number">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>" placeholder="70123456">
                </div>
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
                <label for="order_method">Preferred Ordering Method</label>
                <select name="order_method" id="order_method" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                    <option value="whatsapp" <?php echo ($settings['order_method'] ?? '') === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp</option>
                    <option value="sms" <?php echo ($settings['order_method'] ?? '') === 'sms' ? 'selected' : ''; ?>>SMS (Standard Text)</option>
                </select>
            </div>

            <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Dark Green Banner (3 Texts)</h3>
            <div class="form-group">
                <label for="banner1_t1">Green Banner - Text 1</label>
                <input type="text" name="banner1_t1" value="<?php echo htmlspecialchars($settings['banner1_t1'] ?? 'THANK YOU FOR SUPPORTING LOCAL'); ?>">
            </div>
            <div class="form-group">
                <label for="banner1_t2">Green Banner - Text 2 (Cursive)</label>
                <input type="text" name="banner1_t2" value="<?php echo htmlspecialchars($settings['banner1_t2'] ?? 'Made with fresh ingredients & lots of love'); ?>">
            </div>
            <div class="form-group">
                <label for="banner1_t3">Green Banner - Text 3</label>
                <input type="text" name="banner1_t3" value="<?php echo htmlspecialchars($settings['banner1_t3'] ?? 'AUTHENTIC MEDITERRANEAN FLAVOR'); ?>">
            </div>

            <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Cream Banner (4 Texts)</h3>
            <div class="form-group">
                <label for="banner2_t1">Cream Banner - Text 1</label>
                <input type="text" name="banner2_t1" value="<?php echo htmlspecialchars($settings['banner2_t1'] ?? 'FRESH INGREDIENTS'); ?>">
            </div>
            <div class="form-group">
                <label for="banner2_t2">Cream Banner - Text 2</label>
                <input type="text" name="banner2_t2" value="<?php echo htmlspecialchars($settings['banner2_t2'] ?? 'MADE DAILY'); ?>">
            </div>
            <div class="form-group">
                <label for="banner2_t3">Cream Banner - Text 3</label>
                <input type="text" name="banner2_t3" value="<?php echo htmlspecialchars($settings['banner2_t3'] ?? 'AUTHENTIC RECIPES'); ?>">
            </div>
            <div class="form-group">
                <label for="banner2_t4">Cream Banner - Text 4</label>
                <input type="text" name="banner2_t4" value="<?php echo htmlspecialchars($settings['banner2_t4'] ?? 'MADE WITH LOVE'); ?>">
            </div>

            <div class="form-group">
                <label for="restaurant_description">Restaurant Description</label>
                <br>
                <textarea name="restaurant_description"><?php echo htmlspecialchars($settings['restaurant_description'] ?? ''); ?></textarea>
            </div>
            



            <button type="submit" name="update_settings" class="submit-btn">Save Settings</button>
            <a href="dashboard" class="back-link"><button type="button">BACK</button></a>
        </form>
    </div>
</body>
</html>




