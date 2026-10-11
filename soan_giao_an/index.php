<?php
// ==========================================================
// index.php - Entry point / Bootstrapper của ứng dụng
// ==========================================================

// ----- 1. Bật error reporting (development) -----
// Khi deploy production: đổi E_ALL thành 0 và display_errors thành Off
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----- 2. Khởi động Session an toàn -----
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,           // Session cookie (hết khi đóng browser)
        'path'     => '/',
        'domain'   => '',
        'secure'   => false,       // Đổi thành true khi dùng HTTPS
        'httponly' => true,        // Chống XSS đọc cookie
        'samesite' => 'Lax',      // Chống CSRF
    ]);
    session_start();
}

// ----- 3. Load cấu hình -----
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';

// ----- 4. Load Core Classes -----
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/App.php';

// ----- 5. Khởi chạy Router -----
$app = new App();