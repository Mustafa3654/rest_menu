<?php
// Centralized auth/session/CSRF helpers shared across admin and form pages.
if (!function_exists('start_secure_session')) {
    function start_secure_session(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            if (PHP_VERSION_ID >= 70300) {
                session_set_cookie_params([
                    'lifetime' => 2592000, // 30 days
                    'path' => '/',
                    'secure' => $isHttps,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            } else {
                session_set_cookie_params(
                    2592000, // 30 days
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

