<?php

session_start();

/*
|--------------------------------------------------------------------------
| CẤU HÌNH DATABASE - XAMPP LOCALHOST
|--------------------------------------------------------------------------
| XAMPP mặc định:
| MySQL host : 127.0.0.1
| Database   : edumanage
| User       : root
| Password   : để trống
|--------------------------------------------------------------------------
*/

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'edumanage';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

define('DB_HOST', $dbHost);
define('DB_PORT', $dbPort);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);

const APP_NAME = 'EduManage';


/*
|--------------------------------------------------------------------------
| KẾT NỐI DATABASE
|--------------------------------------------------------------------------
*/

function db(): PDO
{
    static $pdo = null;

    if (!$pdo) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        // Hỗ trợ kết nối bảo mật SSL (ví dụ: Aiven Cloud MySQL)
        $caPath = dirname(__DIR__) . '/ca.pem';
        if (file_exists($caPath)) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    return $pdo;
}


/*
|--------------------------------------------------------------------------
| TRẢ JSON RESPONSE
|--------------------------------------------------------------------------
*/

function json_response($data, int $status = 200): never
{
    http_response_code($status);

    header(
        'Content-Type: application/json; charset=utf-8'
    );

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| KIỂM TRA ĐĂNG NHẬP
|--------------------------------------------------------------------------
*/

function require_login(): array
{
    if (empty($_SESSION['user_id'])) {

        json_response(
            [
                'success' => false,
                'message' => 'Chưa đăng nhập'
            ],
            401
        );
    }

    return [
        'id' => (int) $_SESSION['user_id'],
        'role' => $_SESSION['role'] ?? ''
    ];
}


/*
|--------------------------------------------------------------------------
| KIỂM TRA QUYỀN
|--------------------------------------------------------------------------
*/

function require_role(array $roles): array
{
    $u = require_login();

    if (!in_array($u['role'], $roles, true)) {

        json_response(
            [
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ],
            403
        );
    }

    return $u;
}


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/

function e($v): string
{
    return htmlspecialchars(
        (string) $v,
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| SINH MÃ GIÁO VIÊN
|--------------------------------------------------------------------------
| Định dạng:
| GV-XXXXXXXX
|--------------------------------------------------------------------------
*/

function teacher_code(): string
{
    do {

        $code = 'GV-' . strtoupper(
            substr(
                bin2hex(random_bytes(4)),
                0,
                8
            )
        );

        $st = db()->prepare(
            'SELECT id
             FROM users
             WHERE teacher_code = ?'
        );

        $st->execute([$code]);

    } while ($st->fetch());

    return $code;
}


/*
|--------------------------------------------------------------------------
| SINH MÃ KẾT NỐI HỌC SINH
|--------------------------------------------------------------------------
| Dùng để phụ huynh/học sinh liên kết
| với hồ sơ học sinh.
|--------------------------------------------------------------------------
*/

function connection_code(): string
{
    do {

        $code = strtoupper(
            substr(
                bin2hex(random_bytes(5)),
                0,
                8
            )
        );

        $st = db()->prepare(
            'SELECT id
             FROM students
             WHERE connection_code = ?'
        );

        $st->execute([$code]);

    } while ($st->fetch());

    return $code;
}


/*
|--------------------------------------------------------------------------
| LẤY HỌC SINH THUỘC GIÁO VIÊN
|--------------------------------------------------------------------------
*/

function own_student(
    int $studentId,
    int $teacherId
): array {

    $st = db()->prepare(
        'SELECT *
         FROM students
         WHERE id = ?
         AND teacher_id = ?'
    );

    $st->execute([
        $studentId,
        $teacherId
    ]);

    $s = $st->fetch();

    if (!$s) {

        json_response(
            [
                'success' => false,
                'message' => 'Không tìm thấy học sinh.'
            ],
            404
        );
    }

    return $s;
}


/*
|--------------------------------------------------------------------------
| LẤY DANH SÁCH ID HỌC SINH ĐƯỢC LIÊN KẾT
|--------------------------------------------------------------------------
| Dành cho tài khoản:
| - Học sinh
| - Phụ huynh
|--------------------------------------------------------------------------
*/

function linked_student_ids(array $u): array
{
    /*
    |--------------------------------------------------------------------------
    | TÀI KHOẢN HỌC SINH
    |--------------------------------------------------------------------------
    */

    if ($u['role'] === 'student') {

        $st = db()->prepare(
            'SELECT student_id
             FROM student_accounts
             WHERE user_id = ?'
        );

        $st->execute([
            $u['id']
        ]);

        $r = $st->fetchAll();

        return array_map(
            fn($row) => (int) $row['student_id'],
            $r
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TÀI KHOẢN PHỤ HUYNH
    |--------------------------------------------------------------------------
    */

    if ($u['role'] === 'parent') {

        $st = db()->prepare(
            'SELECT student_id
             FROM student_parents
             WHERE parent_id = ?'
        );

        $st->execute([
            $u['id']
        ]);

        $r = $st->fetchAll();

        return array_map(
            fn($row) => (int) $row['student_id'],
            $r
        );
    }


    return [];
}


/*
|--------------------------------------------------------------------------
| KIỂM TRA TÀI KHOẢN CÓ QUYỀN XEM HỌC SINH
|--------------------------------------------------------------------------
*/

function can_view_student(
    int $studentId,
    array $u
): bool {

    /*
    |--------------------------------------------------------------------------
    | GIÁO VIÊN
    |--------------------------------------------------------------------------
    */

    if ($u['role'] === 'teacher') {

        $st = db()->prepare(
            'SELECT id
             FROM students
             WHERE id = ?
             AND teacher_id = ?'
        );

        $st->execute([
            $studentId,
            $u['id']
        ]);

        return (bool) $st->fetch();
    }


    /*
    |--------------------------------------------------------------------------
    | PHỤ HUYNH / HỌC SINH
    |--------------------------------------------------------------------------
    */

    return in_array(
        $studentId,
        linked_student_ids($u),
        true
    );
}


/*
|--------------------------------------------------------------------------
| CHUYỂN ROLE SANG TÊN TIẾNG VIỆT
|--------------------------------------------------------------------------
*/

function role_label(string $role): string
{
    return [
        'teacher' => 'Giáo viên',
        'parent'  => 'Phụ huynh',
        'student' => 'Học sinh'
    ][$role] ?? $role;
}