<?php
/**
 * One-time password hash migration script.
 * Run ONCE from browser: visit this file directly.
 * It will upgrade all legacy plain-text passwords to bcrypt hashes.
 *
 * IMPORTANT: After running, edit login.php and REMOVE the legacy password
 * compatibility code (the hash_equals plain-text fallback).
 */
include "connection.php";
include "auth.php";
start_secure_session();

$message = "";
$stats = ['total' => 0, 'upgraded' => 0, 'already_hashed' => 0, 'failed' => 0];

if (isset($_POST['run'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid token.</div>";
    } else {
        $result = $conn->query("SELECT user_id, username, userpassword FROM users");
        if (!$result) {
            $message = "<div class='alert alert-danger'>Query failed: " . htmlspecialchars($conn->error) . "</div>";
        } else {
            while ($user = $result->fetch_assoc()) {
                $stats['total']++;
                $stored = (string)($user['userpassword'] ?? '');

                if (password_get_info($stored)['algo'] === 0) {
                    // Legacy plain-text password
                    $newHash = password_hash($stored, PASSWORD_DEFAULT);
                    if ($newHash !== false) {
                        $stmt = $conn->prepare("UPDATE users SET userpassword = ? WHERE user_id = ?");
                        $stmt->bind_param("si", $newHash, $user['user_id']);
                        if ($stmt->execute()) {
                            $stats['upgraded']++;
                        } else {
                            $stats['failed']++;
                            error_log("Password upgrade failed for user_id: " . $user['user_id']);
                        }
                        $stmt->close();
                    } else {
                        $stats['failed']++;
                    }
                } else {
                    $stats['already_hashed']++;
                }
            }

            if ($stats['upgraded'] > 0) {
                $message = "<div class='alert alert-success'>"
                    . "Migration complete! Upgraded: {$stats['upgraded']}, "
                    . "Already hashed: {$stats['already_hashed']}, "
                    . "Failed: {$stats['failed']}"
                    . "</div>";
            } else {
                $message = "<div class='alert alert-info'>"
                    . "No legacy passwords found. All {$stats['already_hashed']} users already have hashed passwords."
                    . "</div>";
            }
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
    <title>Password Migration</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 500px; width: 90%; }
        h1 { color: #1a2a6c; margin-bottom: 10px; }
        .alert { padding: 15px; border-radius: 8px; margin: 20px 0; font-weight: 500; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-info { background: #d1ecf1; color: #0c5460; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .btn { display: inline-block; background: #1a2a6c; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 600; text-decoration: none; }
        .btn:hover { background: #15215a; }
        .info { font-size: 14px; color: #666; line-height: 1.6; margin: 20px 0; }
        .warning { font-size: 13px; color: #856404; background: #fff3cd; padding: 12px; border-radius: 6px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Password Hash Migration</h1>
        <?php if ($message) echo $message; ?>
        <p class="info">
            This script upgrades all legacy plain-text passwords in the <code>users</code> table
            to secure bcrypt hashes. This is a <strong>one-time operation</strong>.
        </p>
        <div class="warning">
            <strong>Before running:</strong><br>
            1. Back up your <code>users</code> table<br>
            2. After running, edit <code>login.php</code> and remove the plain-text
               password fallback (the <code>hash_equals($storedPassword, $password)</code> line)
        </div>
        <form method="POST">
            <?php echo csrf_input(); ?>
            <button type="submit" name="run" class="btn">Run Migration</button>
        </form>
    </div>
</body>
</html>
