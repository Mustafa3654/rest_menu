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
        $message = "<div class='alert-custom alert-custom-error'>Invalid request token. Please refresh and try again.</div>";
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

            $message = "<div class='alert-custom alert-custom-success'>Settings updated successfully!</div>";
            // Refresh settings cache
            $settings = get_settings();
        } else {
            $message = "<div class='alert-custom alert-custom-error'>Error updating settings: " . htmlspecialchars($stmt->error) . "</div>";
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
    <link rel="stylesheet" href="../style/tailwind.css">
</head> 
<body class="bg-[#F7F5EA] font-poppins min-h-screen flex items-center justify-center py-10">
    <div class="bg-white p-8 rounded-xl shadow-lg w-[500px] mx-auto">
        <h1 class="text-[#42522B] text-center text-2xl font-bold mb-4">Restaurant Settings</h1>
        <div class="text-center mb-5">
            <a href="manageGallery" class="no-underline">
                <button type="button" class="w-full py-3 bg-[#42522B] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#2b3a1d] mb-2.5">MANAGE GALLERY PHOTOS</button>
            </a>
        </div>
        <?php echo $message; ?>
        <form action="editSettings" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            <?php echo csrf_input(); ?>

            <div class="flex flex-col gap-2">
                <label for="restaurant_name" class="font-bold text-sm">Restaurant Name</label>
                <input type="text" name="restaurant_name" value="<?php echo htmlspecialchars($settings['restaurant_name'] ?? ''); ?>" required class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="restaurant_logo" class="font-bold text-sm">Restaurant Logo</label>
                <input type="file" name="restaurant_logo" accept="image/*" class="text-sm">
                <?php if (!empty($settings['restaurant_logo'])): ?>
                    <img src="../<?php echo htmlspecialchars($settings['restaurant_logo']); ?>" class="mt-2.5 max-w-[150px] rounded-lg border border-gray-300" alt="Current Logo">
                <?php endif; ?>
            </div>

            <div class="flex flex-col gap-2">
                <label for="home_bg" class="font-bold text-sm">Home Page Hero Background</label>
                <input type="file" name="home_bg" accept="image/*" class="text-sm">
                <?php if (!empty($settings['home_bg'])): ?>
                    <img src="../<?php echo htmlspecialchars($settings['home_bg']); ?>" class="mt-2.5 max-w-[150px] rounded-lg border border-gray-300" alt="Home BG">
                <?php endif; ?>
            </div>

            <div class="flex flex-col gap-2">
                <label for="menu_bg" class="font-bold text-sm">Menu Page Hero Background</label>
                <input type="file" name="menu_bg" accept="image/*" class="text-sm">
                <?php if (!empty($settings['menu_bg'])): ?>
                    <img src="../<?php echo htmlspecialchars($settings['menu_bg']); ?>" class="mt-2.5 max-w-[150px] rounded-lg border border-gray-300" alt="Menu BG">
                <?php endif; ?>
            </div>

            <div class="flex flex-col gap-2">
                <label for="contact_bg" class="font-bold text-sm">Contact Page Hero Background</label>
                <input type="file" name="contact_bg" accept="image/*" class="text-sm">
                <?php if (!empty($settings['contact_bg'])): ?>
                    <img src="../<?php echo htmlspecialchars($settings['contact_bg']); ?>" class="mt-2.5 max-w-[150px] rounded-lg border border-gray-300" alt="Contact BG">
                <?php endif; ?>
            </div>

            <div class="flex flex-col gap-2">
                <label for="opening_title" class="font-bold text-sm">Opening Title (e.g., Open Daily)</label>
                <input type="text" name="opening_title" value="<?php echo htmlspecialchars($settings['opening_title'] ?? 'Open Daily'); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="opening_hours" class="font-bold text-sm">Opening Hours</label>
                <input type="text" name="opening_hours" value="<?php echo htmlspecialchars($settings['opening_hours'] ?? ''); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="restaurant_phone" class="font-bold text-sm">Phone Number</label>
                <input type="text" name="restaurant_phone" value="<?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="restaurant_email" class="font-bold text-sm">Email Address</label>
                <input type="email" name="restaurant_email" value="<?php echo htmlspecialchars($settings['restaurant_email'] ?? ''); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex gap-2.5">
                <div class="flex flex-col gap-2 flex-[0_0_100px]">
                    <label for="country_code" class="font-bold text-sm">Country Code</label>
                    <input type="text" name="country_code" value="<?php echo htmlspecialchars($settings['country_code'] ?? '+1'); ?>" placeholder="+1" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
                </div>
                <div class="flex flex-col gap-2 flex-1">
                    <label for="whatsapp_number" class="font-bold text-sm">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>" placeholder="70123456" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label for="restaurant_address" class="font-bold text-sm">Address</label>
                <input type="text" name="restaurant_address" value="<?php echo htmlspecialchars($settings['restaurant_address'] ?? ''); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="restaurant_maps" class="font-bold text-sm">Location Map Link</label>
                <input type="url" name="restaurant_maps" value="<?php echo htmlspecialchars($settings['restaurant_maps'] ?? ''); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="instagram_url" class="font-bold text-sm">Instagram URL</label>
                <input type="url" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="facebook_url" class="font-bold text-sm">Facebook URL</label>
                <input type="url" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="order_method" class="font-bold text-sm">Preferred Ordering Method</label>
                <select name="order_method" id="order_method" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
                    <option value="whatsapp" <?php echo ($settings['order_method'] ?? '') === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp</option>
                    <option value="sms" <?php echo ($settings['order_method'] ?? '') === 'sms' ? 'selected' : ''; ?>>SMS (Standard Text)</option>
                </select>
            </div>

            <h3 class="mt-8 mb-4 pb-2 text-[#42522B] font-bold text-lg border-b-2" style="border-color: #CBB58B;">Dark Green Banner (3 Texts)</h3>

            <div class="flex flex-col gap-2">
                <label for="banner1_t1" class="font-bold text-sm">Green Banner - Text 1</label>
                <input type="text" name="banner1_t1" value="<?php echo htmlspecialchars($settings['banner1_t1'] ?? 'THANK YOU FOR SUPPORTING LOCAL'); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="banner1_t2" class="font-bold text-sm">Green Banner - Text 2 (Cursive)</label>
                <input type="text" name="banner1_t2" value="<?php echo htmlspecialchars($settings['banner1_t2'] ?? 'Made with fresh ingredients & lots of love'); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="banner1_t3" class="font-bold text-sm">Green Banner - Text 3</label>
                <input type="text" name="banner1_t3" value="<?php echo htmlspecialchars($settings['banner1_t3'] ?? 'AUTHENTIC MEDITERRANEAN FLAVOR'); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <h3 class="mt-8 mb-4 pb-2 text-[#42522B] font-bold text-lg border-b-2" style="border-color: #CBB58B;">Cream Banner (4 Texts)</h3>

            <div class="flex flex-col gap-2">
                <label for="banner2_t1" class="font-bold text-sm">Cream Banner - Text 1</label>
                <input type="text" name="banner2_t1" value="<?php echo htmlspecialchars($settings['banner2_t1'] ?? 'FRESH INGREDIENTS'); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="banner2_t2" class="font-bold text-sm">Cream Banner - Text 2</label>
                <input type="text" name="banner2_t2" value="<?php echo htmlspecialchars($settings['banner2_t2'] ?? 'MADE DAILY'); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="banner2_t3" class="font-bold text-sm">Cream Banner - Text 3</label>
                <input type="text" name="banner2_t3" value="<?php echo htmlspecialchars($settings['banner2_t3'] ?? 'AUTHENTIC RECIPES'); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="banner2_t4" class="font-bold text-sm">Cream Banner - Text 4</label>
                <input type="text" name="banner2_t4" value="<?php echo htmlspecialchars($settings['banner2_t4'] ?? 'MADE WITH LOVE'); ?>" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="restaurant_description" class="font-bold text-sm">Restaurant Description</label>
                <textarea name="restaurant_description" class="p-2.5 border border-gray-300 rounded-md text-sm w-full resize-y"><?php echo htmlspecialchars($settings['restaurant_description'] ?? ''); ?></textarea>
            </div>

            <button type="submit" name="update_settings" class="w-full py-3 bg-[#42522B] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#2b3a1d]">Save Settings</button>
            <a href="dashboard" class="block no-underline"><button type="button" class="w-full py-3 bg-[#6c757d] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#5a6268]">BACK</button></a>
        </form>
    </div>
</body>
</html>
