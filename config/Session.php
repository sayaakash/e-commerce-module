<?php
class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Security settings
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', '1'); // Set to '0' for HTTP development
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.gc_maxlifetime', '3600'); // 1 hour

            session_start();

            // Regenerate session ID for security
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
                self::regenerate();
            } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
                self::regenerate();
                $_SESSION['created'] = time();
            }
        }
    }

    public static function regenerate(): void {
        session_regenerate_id(true);
    }

    public static function destroy(): void {
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }

    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }
}
