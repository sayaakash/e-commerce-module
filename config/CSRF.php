<?php
class CSRF {
    private const TOKEN_KEY = 'csrf_token';
    private const TOKEN_LENGTH = 32;

    public static function generateToken(): string {
        if (!Session::has(self::TOKEN_KEY)) {
            $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
            Session::set(self::TOKEN_KEY, $token);
        }
        return Session::get(self::TOKEN_KEY);
    }

    public static function validateToken(string $token): bool {
        $storedToken = Session::get(self::TOKEN_KEY);
        return hash_equals($storedToken, $token);
    }

    public static function getTokenField(): string {
        $token = self::generateToken();
        return '<input type="hidden" name="' . self::TOKEN_KEY . '" value="' . htmlspecialchars($token) . '">';
    }

    public static function validateRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[self::TOKEN_KEY] ?? '';

            if (!self::validateToken($token)) {
                http_response_code(403);
                die('CSRF token validation failed');
            }

            // Regenerate token after successful validation
            Session::remove(self::TOKEN_KEY);
        }
    }
}
