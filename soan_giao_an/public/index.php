<?php
// ==========================================================
// public/index.php
// Front Controller - Luồng điều hướng (Routing) duy nhất
// ==========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nạp tệp cấu hình hằng số toàn cục
require_once __DIR__ . '/../config/constants.php';

// Lấy chuỗi URL điều hướng
$url = $_GET['url'] ?? 'quiz/index';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$parts = explode('/', $url);

// Xác định Controller, Action và Parameter
$controllerName = ucfirst($parts[0] ?? 'Quiz') . 'Controller';
$actionName     = $parts[1] ?? 'index';
$param          = $parts[2] ?? null;

$controllerFile = ROOT_PATH . '/app/Controllers/' . $controllerName . '.php';

// Kiểm tra tệp Controller có tồn tại hay không
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    if (class_exists($controllerName)) {
        $controller = new $controllerName();

        if (method_exists($controller, $actionName)) {
            if ($param !== null) {
                $controller->$actionName($param);
            } else {
                $controller->$actionName();
            }
        } else {
            http_response_code(404);
            echo '<h1 style="text-align:center; margin-top:50px; font-family:sans-serif; color:#ef4444;">404 - Phương thức không tồn tại</h1>';
        }
    } else {
        http_response_code(404);
        echo '<h1 style="text-align:center; margin-top:50px; font-family:sans-serif; color:#ef4444;">404 - Lớp Controller không hợp lệ</h1>';
    }
} else {
    http_response_code(404);
    echo '<h1 style="text-align:center; margin-top:50px; font-family:sans-serif; color:#ef4444;">404 - Trang không tồn tại</h1>';
}