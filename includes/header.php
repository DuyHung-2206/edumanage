<?php 
require_once __DIR__.'/config/config.php'; 
if(empty($_SESSION['user_id'])){
    header('Location:login.php');
    exit;
} 
$role = $_SESSION['role']; 
$readonly = ($role !== 'teacher');

// Đảm bảo giáo viên luôn có teacher_code trong session và database
$teacherCode = '';
if($role === 'teacher'){
    if(empty($_SESSION['teacher_code'])){
        $st = db()->prepare('SELECT teacher_code FROM users WHERE id=?');
        $st->execute([$_SESSION['user_id']]);
        $row = $st->fetch();
        if(!empty($row['teacher_code'])){
            $_SESSION['teacher_code'] = $row['teacher_code'];
        } else {
            $newCode = teacher_code();
            db()->prepare('UPDATE users SET teacher_code=? WHERE id=?')->execute([$newCode, $_SESSION['user_id']]);
            $_SESSION['teacher_code'] = $newCode;
        }
    }
    $teacherCode = $_SESSION['teacher_code'];
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>EduManage - Quản lý học tập</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
