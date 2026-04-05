<?php
// Centralized auth/session/CSRF helpers shared across admin and form pages.
if (!function_exists('start_secure_session')) {
    function start_secure_session(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            if (PHP_VERSION_ID >= 70300) {
                session_set_cookie_params([
                    'lifetime' => 0,
                    'path' => '/',
                    'secure' => $isHttps,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            } else {
                session_set_cookie_params(
                    0,
                    '/; samesite=Lax',
                    '',
                    $isHttps,
                    true
                );
            }
            session_start();
        }
    }
}

if (!function_exists('is_admin_logged_in')) {
    function is_admin_logged_in(): bool
    {
        return isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
    }
}

if (!function_exists('require_admin')) {
    function require_admin(string $redirect = 'index.php'): void
    {
        if (!is_admin_logged_in()) {
            header('Location: ' . $redirect);
            exit;
        }
    }
}

if (!function_exists('ensure_csrf_token')) {
    function ensure_csrf_token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token(?string $token): bool
    {
        if (!is_string($token) || $token === '' || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('csrf_input')) {
    function csrf_input(): string
    {
        $token = ensure_csrf_token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

// -------------------------
// Settings caching (per-session)
// -------------------------
if (!function_exists('get_settings')) {
    /**
     * Returns cached settings from session, or loads from DB if not cached.
     * Cache is invalidated when settings are updated.
     */
    function get_settings(): ?array
    {
        global $conn;

        if (!isset($_SESSION['settings_cache']) || $_SESSION['settings_cache_expires'] < time()) {
            $result = $conn->query("SELECT * FROM settings LIMIT 1");
            $_SESSION['settings_cache'] = $result ? $result->fetch_assoc() : null;
            $_SESSION['settings_cache_expires'] = time() + 300; // 5 minutes
        }

        return $_SESSION['settings_cache'];
    }
}

if (!function_exists('invalidate_settings_cache')) {
    function invalidate_settings_cache(): void
    {
        unset($_SESSION['settings_cache'], $_SESSION['settings_cache_expires']);
    }
}

// -------------------------
// Audit logging
// -------------------------
if (!function_exists('log_audit')) {
    /**
     * Log an admin action to the audit_log table.
     * Does nothing if the table doesn't exist (graceful degradation).
     */
    function log_audit(string $action, string $entity, ?int $entityId = null, ?string $details = null): void
    {
        global $conn;

        static $tableChecked = false;
        static $tableExists = null;

        if ($tableChecked === false) {
            $tableChecked = true;
            $res = $conn->query("SHOW TABLES LIKE 'audit_log'");
            $tableExists = ($res && $res->num_rows > 0);
        }

        if (!$tableExists) return;

        $username = $_SESSION['user_name'] ?? 'unknown';
        $stmt = $conn->prepare(
            "INSERT INTO audit_log (username, action, entity, entity_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt->bind_param("ssssss", $username, $action, $entity, $entityId, $details, $ip);
        @$stmt->execute(); // Use @ to suppress errors if table is gone
        $stmt->close();
    }
}

if (!function_exists('init_audit_log_table')) {
    function init_audit_log_table(): bool
    {
        global $conn;

        $sql = "CREATE TABLE IF NOT EXISTS audit_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL,
            action VARCHAR(50) NOT NULL,
            entity VARCHAR(50) NOT NULL,
            entity_id INT DEFAULT NULL,
            details TEXT DEFAULT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_entity (entity),
            INDEX idx_action (action),
            INDEX idx_username (username),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        return $conn->query($sql) === true;
    }
}
