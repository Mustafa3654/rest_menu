<?php
include "../includes/connection.php";
include "../includes/auth.php";
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
        $message = "<div class='alert-custom alert-custom-error'>Invalid request token. Please refresh and try again.</div>";
    } else {
        $chat_id = trim($_POST['chat_id'] ?? '');
        $bot_token = trim($_POST['bot_token'] ?? '');

        // Persist settings
        if ($settings) {
            $stmt = $conn->prepare("UPDATE settings SET chat_id = ?, bot_token = ? WHERE id = ?");
            $stmt->bind_param("ssi", $chat_id, $bot_token, $settings['id']);
            
            if ($stmt->execute()) {
                invalidate_settings_cache();
                $message = "<div class='alert-custom alert-custom-success'>Telegram Settings updated successfully!</div>";
                $settings = get_settings();
            } else {
                $message = "<div class='alert-custom alert-custom-error'>Error updating settings: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        } else {
            $message = "<div class='alert-custom alert-custom-error'>Settings configuration not found. Please save general settings first.</div>";
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
    <link rel="stylesheet" href="../style/tailwind.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head> 
<body class="bg-[#F7F5EA] font-poppins min-h-screen flex items-center justify-center py-10">
    <div class="bg-white p-8 rounded-xl shadow-lg w-[500px] mx-auto">
        <i class="fab fa-telegram text-[40px] text-[#0088cc] text-center block mb-2.5"></i>
        <h1 class="text-[#42522B] text-center text-2xl font-bold mb-6">Telegram Config</h1>
        <?php echo $message; ?>
        <form action="editTelegram" method="POST" class="flex flex-col gap-4">
            <?php echo csrf_input(); ?>

            <div class="flex flex-col gap-2">
                <label for="chat_id" class="font-bold text-sm">Telegram Chat ID</label>
                <input type="text" name="chat_id" value="<?php echo htmlspecialchars($settings['chat_id'] ?? ''); ?>" placeholder="e.g. -100123456789" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label for="bot_token" class="font-bold text-sm">Telegram Bot Token</label>
                <input type="text" name="bot_token" value="<?php echo htmlspecialchars($settings['bot_token'] ?? ''); ?>" placeholder="e.g. 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11" class="p-2.5 border border-gray-300 rounded-md text-sm w-full">
            </div>

            <button type="submit" name="update_telegram" class="w-full py-3 bg-[#42522B] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#2b3a1d]">Save Telegram Settings</button>
            <a href="dashboard" class="block no-underline"><button type="button" class="w-full py-3 bg-[#6c757d] text-white font-bold rounded-lg cursor-pointer transition-colors hover:bg-[#5a6268]">BACK</button></a>
        </form>
    </div>
</body>
</html>
