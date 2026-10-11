<?php
// ==========================================================
// app/Controllers/StudentController.php
// Điều hướng về hệ thống Quản lý học sinh tập trung của EduManage
// ==========================================================

require_once ROOT_PATH . '/app/Controllers/AuthController.php';

class StudentController
{
    public function index(): void
    {
        header('Location: ' . EDUMANAGE_URL . '/index.php');
        exit;
    }

    public function profile(): void
    {
        header('Location: ' . EDUMANAGE_URL . '/index.php');
        exit;
    }

    public function add(): void
    {
        header('Location: ' . EDUMANAGE_URL . '/index.php');
        exit;
    }

    public function delete(): void
    {
        header('Location: ' . EDUMANAGE_URL . '/index.php');
        exit;
    }
}