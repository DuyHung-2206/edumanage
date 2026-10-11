<?php

// ==========================================================
// app/Controllers/AuthController.php
// Quản lý Đăng nhập, Đăng ký (Tích hợp EduManage) & Phân quyền
// ==========================================================

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    // ==========================================================
    // Trang Đăng nhập chung
    // ==========================================================

    public function login(): void
    {
        if (self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/quiz/index');
            exit;
        }

        // Chuyển hướng sang trang đăng nhập thống nhất của EduManage
        header('Location: ' . EDUMANAGE_URL . '/login.php');
        exit;
    }

    // ==========================================================
    // Trang Đăng ký
    // ==========================================================

    public function register(): void
    {
        if (self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/quiz/index');
            exit;
        }

        // Chuyển hướng sang trang đăng ký thống nhất của EduManage
        header('Location: ' . EDUMANAGE_URL . '/login.php');
        exit;
    }

    // ==========================================================
    // Đăng xuất
    // ==========================================================

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION[SESSION_USER_KEY]);
        unset($_SESSION['user_id']);
        session_destroy();

        header('Location: ' . EDUMANAGE_URL . '/login.php');
        exit;
    }

    // ==========================================================
    // Kiểm tra người dùng đã đăng nhập
    // ==========================================================

    public static function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return !empty($_SESSION['user_id']) || !empty($_SESSION[SESSION_USER_KEY]);
    }

    // ==========================================================
    // Lấy thông tin người dùng hiện tại
    // ==========================================================

    public static function currentUser(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user_id'])) {
            return [
                'id' => (int)$_SESSION['user_id'],
                'user_id' => (int)$_SESSION['user_id'],
                'username' => $_SESSION['user_name'] ?? 'Giáo viên',
                'full_name' => $_SESSION['user_name'] ?? 'Giáo viên',
                'fullName' => $_SESSION['user_name'] ?? 'Giáo viên',
                'role' => $_SESSION['role'] ?? 'teacher',
                'teacher_code' => $_SESSION['teacher_code'] ?? ''
            ];
        }

        return $_SESSION[SESSION_USER_KEY] ?? null;
    }
}
?>
