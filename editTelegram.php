<?php
include "connection.php";
include "auth.php";
start_secure_session();
require_admin();

// -------------------------
// Load current settings (cached)
// -------------------------
$settings = get_settings();
$message = "";

// -------------------------
// Handle form submission
// -------------------------
if (isset($_POST['update_telegram'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token. Please refresh and try again.</div>";
    } else {
        $chat_id = trim($_POST['chat_id'] ?? '');
        $bot_token = trim($_POST['bot_token'] ?? '');

        // Persist settings
        if ($settings) {
            $stmt = $conn->prepare("UPDATE settings SET chat_id = ?, bot_token = ? WHERE id = ?");
            $stmt->bind_param("ssi", $chat_id, $bot_token, $settings['id']);
            
            if ($stmt->execute()) {
                invalidate_settings_cache();
                $message = "<div class='alert alert-success'>Telegram Settings updated successfully!</div>";
                $settings = get_settings();
            } else {
                $message = "<div class='alert alert-danger'>Error updating settings: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        } else {
            $message = "<div class='alert alert-danger'>Settings configuration not found. Please save general settings first.</div>";
        }
    }
}

$csrfToken = ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Telegram Settings</title>
    <link rel="stylesheet" href="style/admin_form.css">
    <style>
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; font-weight: 600; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
        .form-group input { width: 100%; box-sizing: border-box; }
        .submit-btn { width: 100%; margin-top: 10px; }
        .back-link button { width: 100%; margin-top: 10px; background: #e74c3c; }
        .back-link button:hover { background: #c0392b; }
        .tg-icon {
            font-size: 40px;
            color: #0088cc;
            text-align: center;
            display: block;
            margin-bottom: 10px;
        }
    </style>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head> 
<body>
    <div class="form-container">
        <i class="fab fa-telegram tg-icon"></i>
        <h1>Telegram Config</h1>
        <?php echo $message; ?>
        <form action="editTelegram.php" method="POST">
            <?php echo csrf_input(); ?>

            <div class="form-group">
                <label for="chat_id">Telegram Chat ID</label>
                <input type="text" name="chat_id" value="<?php echo htmlspecialchars($settings['chat_id'] ?? ''); ?>" placeholder="e.g. -100123456789">
            </div>

            <div class="form-group">
                <label for="bot_token">Telegram Bot Token</label>
                <input type="text" name="bot_token" value="<?php echo htmlspecialchars($settings['bot_token'] ?? ''); ?>" placeholder="e.g. 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
            </div>

            <button type="submit" name="update_telegram" class="submit-btn">Save Telegram Settings</button>
            <a href="dashboard.php" class="back-link"><button type="button">BACK</button></a>
        </form>
    </div>
</body>
</html>
