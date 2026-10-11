<?php
/**
 * Migration script: Bổ sung cột teacher_code vào bảng users
 * và tự động sinh mã GV-XXXXXXXX cho toàn bộ giáo viên cũ.
 */
require_once __DIR__ . '/config/config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Tiến trình Migration: Bổ sung Mã giáo viên (teacher_code)</h2>";

try {
    $pdo = db();

    // 1. Kiểm tra cột teacher_code trong bảng users
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'teacher_code'");
    $col = $stmt->fetch();

    if (!$col) {
        $pdo->exec("ALTER TABLE users ADD COLUMN teacher_code VARCHAR(20) NULL UNIQUE AFTER role");
        echo "<p style='color:green;'>✓ Đã thêm cột <b>teacher_code</b> (VARCHAR(20) UNIQUE NULL) vào bảng <b>users</b>.</p>";
    } else {
        echo "<p style='color:blue;'>ℹ Cột <b>teacher_code</b> đã tồn tại trong bảng users.</p>";
    }

    // 2. Lấy danh sách giáo viên chưa có mã
    $stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'teacher' AND (teacher_code IS NULL OR teacher_code = '')");
    $teachers = $stmt->fetchAll();

    if (empty($teachers)) {
        echo "<p style='color:green;'>✓ Tất cả giáo viên đều đã có mã định danh GV-XXXXXXXX!</p>";
    } else {
        echo "<p>Đang tiến hành cấp mã cho " . count($teachers) . " giáo viên cũ...</p>";
        $updateStmt = $pdo->prepare("UPDATE users SET teacher_code = ? WHERE id = ?");

        $count = 0;
        foreach ($teachers as $t) {
            $code = teacher_code();
            $updateStmt->execute([$code, $t['id']]);
            echo "<p>+ Giáo viên: <b>" . htmlspecialchars($t['name']) . "</b> (" . htmlspecialchars($t['email']) . ") -> Mã mới: <b style='color:#2563eb; font-family:monospace;'>" . $code . "</b></p>";
            $count++;
        }
        echo "<p style='color:green; font-weight:bold;'>✓ Đã cấp thành công mã giáo viên cho $count tài khoản!</p>";
    }

    echo "<hr><p style='color:green;'><b>Migration hoàn tất thành công!</b> Bạn có thể quay lại <a href='index.php'>Trang chủ</a>.</p>";

} catch (Throwable $e) {
    echo "<p style='color:red;'><b>Lỗi Migration:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
}
