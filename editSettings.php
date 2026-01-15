<?php
include "connection.php";
session_start();

if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] !== true) {
    header("Location: index.php");
    exit();
}

// Fetch current settings
$settingsQuery = "SELECT * FROM settings LIMIT 1";
$settingsResult = $conn->query($settingsQuery);
$settings = $settingsResult ? $settingsResult->fetch_assoc() : null;

$message = "";

if (isset($_POST['update_settings'])) {
    $name = $_POST['restaurant_name'];
    $email = $_POST['restaurant_email'];
    $phone = $_POST['restaurant_phone'];
    $address = $_POST['restaurant_address'];
    $maps = $_POST['restaurant_maps'];
    $desc = $_POST['restaurant_description'];
    $hours = $_POST['opening_hours'];
    $whatsapp = $_POST['whatsapp_number'];
    $insta = $_POST['instagram_url'];
    $fb = $_POST['facebook_url'];
    $opening_title = $_POST['opening_title'];
    $chat_id = $_POST['chat_id'];
    $bot_token = $_POST['bot_token'];

    // Handle Logo Upload
    $logo_path = $settings['restaurant_logo'] ?? '';
    if (!empty($_FILES['restaurant_logo']['name'])) {
        $target_dir = "bgs/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = "logo_" . time() . "_" . basename($_FILES["restaurant_logo"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["restaurant_logo"]["tmp_name"], $target_file)) {
            $logo_path = $target_file;
        }
    }

    // Handle Home Background Upload
    $home_bg_path = $settings['home_bg'] ?? '';
    if (!empty($_FILES['home_bg']['name'])) {
        $target_dir = "bgs/";
        $file_name = "home_" . time() . "_" . basename($_FILES["home_bg"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["home_bg"]["tmp_name"], $target_file)) {
            $home_bg_path = $target_file;
        }
    }

    // Handle Menu Background Upload
    $menu_bg_path = $settings['menu_bg'] ?? '';
    if (!empty($_FILES['menu_bg']['name'])) {
        $target_dir = "bgs/";
        $file_name = "menu_" . time() . "_" . basename($_FILES["menu_bg"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["menu_bg"]["tmp_name"], $target_file)) {
            $menu_bg_path = $target_file;
        }
    }

    // Handle Contact Background Upload
    $contact_bg_path = $settings['contact_bg'] ?? '';
    if (!empty($_FILES['contact_bg']['name'])) {
        $target_dir = "bgs/";
        $file_name = "contact_" . time() . "_" . basename($_FILES["contact_bg"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["contact_bg"]["tmp_name"], $target_file)) {
            $contact_bg_path = $target_file;
        }
    }

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
            bot_token = ?
            WHERE id = ?");
        $stmt->bind_param("sssssssssssssssssi", $name, $logo_path, $home_bg_path, $menu_bg_path,$contact_bg_path, $email, $phone, $address, $maps, $desc, $hours, $whatsapp, $insta, $fb, $opening_title, $chat_id, $bot_token, $settings['id']);
    } else {
        $stmt = $conn->prepare("INSERT INTO settings (restaurant_name, restaurant_logo, home_bg, menu_bg, contact_bg, restaurant_email, restaurant_phone, restaurant_address, restaurant_maps, restaurant_description, opening_hours, whatsapp_number, instagram_url, facebook_url, opening_title, chat_id, bot_token) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssssss", $name, $logo_path, $home_bg_path, $menu_bg_path,$contact_bg_path,$email,$phone,$address,$maps,$desc,$hours,$whatsapp,$insta,$fb,$opening_title,$chat_id,$bot_token);
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

            <button type="submit" name="update_settings" class="submit-btn">Save Settings</button>
            <a href="dashboard.php" class="back-link"><button type="button">BACK</button></a>
        </form>
    </div>
</body>
</html>
