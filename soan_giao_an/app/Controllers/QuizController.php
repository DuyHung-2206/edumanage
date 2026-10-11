<?php

require_once ROOT_PATH . '/config/constants.php';

class QuizController
{
    private function getDB()
    {
        static $pdo = null;

        if ($pdo === null) {
            try {
                $port = defined('DB_PORT') ? DB_PORT : (getenv('DB_PORT') ?: '3306');
                $dsn = 'mysql:host=' . DB_HOST .
                       ';port=' . $port .
                       ';dbname=' . DB_NAME .
                       ';charset=' . DB_CHARSET;

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];

                $caPath = dirname(dirname(ROOT_PATH)) . '/ca.pem';
                if (file_exists($caPath)) {
                    $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
                    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                }

                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die('Lỗi kết nối database: ' . $e->getMessage());
            }
        }

        return $pdo;
    }

    private function getCurrentUser()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Tự động nhận diện session của EduManage
        if (isset($_SESSION['user_id'])) {
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

        if (isset($_SESSION[SESSION_USER_KEY])) {
            return $_SESSION[SESSION_USER_KEY];
        }

        return null;
    }

    private function getTeacherId()
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return null;
        }

        if (is_array($user)) {
            if (isset($user['id'])) {
                return (int)$user['id'];
            }

            if (isset($user['user_id'])) {
                return (int)$user['user_id'];
            }
        }

        if (is_numeric($user)) {
            return (int)$user;
        }

        return null;
    }

    private function requireLogin()
    {
        if (!$this->getCurrentUser()) {
            if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.']);
                exit;
            }
            header('Location: ' . EDUMANAGE_URL . '/login.php');
            exit;
        }
    }

    private function requireTeacher()
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.']);
                exit;
            }
            header('Location: ' . EDUMANAGE_URL . '/login.php');
            exit;
        }

        $role = null;

        if (is_array($user) && isset($user['role'])) {
            $role = $user['role'];
        }

        if ($role !== 'teacher') {
            http_response_code(403);
            die('Bạn không có quyền thực hiện chức năng này.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DANH SÁCH ĐỀ
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $this->requireLogin();

        $pdo = $this->getDB();

        $teacherId = $this->getTeacherId();
        $user = $this->getCurrentUser();

        if (
            is_array($user) &&
            isset($user['role']) &&
            $user['role'] === 'teacher'
        ) {
            $sql = "
                SELECT
                    q.id,
                    q.teacher_id,
                    q.grade,
                    q.subject,
                    q.lesson_name,
                    q.duration,
                    q.format,
                    q.created_at,
                    IFNULL(u.name, 'Giáo viên') AS teacher_name
                FROM quizzes q
                LEFT JOIN users u
                    ON q.teacher_id = u.id
                WHERE q.teacher_id = :teacher_id
                ORDER BY q.created_at DESC
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':teacher_id' => $teacherId
            ]);
        } else {
            $sql = "
                SELECT
                    q.id,
                    q.teacher_id,
                    q.grade,
                    q.subject,
                    q.lesson_name,
                    q.duration,
                    q.format,
                    q.created_at,
                    IFNULL(u.name, 'Giáo viên') AS teacher_name
                FROM quizzes q
                LEFT JOIN users u
                    ON q.teacher_id = u.id
                ORDER BY q.created_at DESC
            ";

            $stmt = $pdo->query($sql);
        }

        $quizzes = $stmt->fetchAll();

        require ROOT_PATH . '/app/Views/quizzes/index.php';
    }

    /*
    |--------------------------------------------------------------------------
    | TRANG TẠO ĐỀ
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $this->requireTeacher();

        require ROOT_PATH . '/app/Views/quizzes/create.php';
    }

    /*
    |--------------------------------------------------------------------------
    | AI TẠO ĐỀ
    |--------------------------------------------------------------------------
    */

    public function generate()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $this->requireTeacher();

            $teacherId = $this->getTeacherId();

            if (!$teacherId) {
                throw new Exception(
                    'Không xác định được tài khoản giáo viên.'
                );
            }

            $grade = isset($_POST['grade'])
                ? trim($_POST['grade'])
                : '';

            $subject = isset($_POST['subject'])
                ? trim($_POST['subject'])
                : '';

            $lessonName = isset($_POST['topic'])
                ? trim($_POST['topic'])
                : '';

            if ($lessonName === '' && isset($_POST['lesson_name'])) {
                $lessonName = trim($_POST['lesson_name']);
            }

            $duration = isset($_POST['duration'])
                ? trim($_POST['duration'])
                : '15p';

            $format = isset($_POST['format'])
                ? trim($_POST['format'])
                : 'mixed';

            $numQuestions = isset($_POST['num_questions'])
                ? (int)$_POST['num_questions']
                : 10;

            $difficulty = isset($_POST['difficulty'])
                ? trim($_POST['difficulty'])
                : 'Trung bình';

            if ($grade === '') {
                throw new Exception('Vui lòng chọn lớp.');
            }

            if ($subject === '') {
                throw new Exception('Vui lòng nhập môn học.');
            }

            if ($lessonName === '') {
                throw new Exception(
                    'Vui lòng nhập tên bài học hoặc chủ đề.'
                );
            }

            if ($numQuestions <= 0) {
                $numQuestions = 10;
            }

            if ($numQuestions > 50) {
                $numQuestions = 50;
            }

            switch ($format) {
                case 'multiple_choice':
                    $formatDescription =
                        '100% câu hỏi trắc nghiệm. Mỗi câu có 4 đáp án A, B, C, D.';
                    break;

                case 'essay':
                    $formatDescription =
                        '100% câu hỏi tự luận. Câu hỏi phải phù hợp với học sinh tiểu học.';
                    break;

                case 'mixed':
                default:
                    $formatDescription =
                        'Kết hợp câu hỏi trắc nghiệm và tự luận.';
                    break;
            }

            $prompt = <<<PROMPT
Bạn là chuyên gia ra đề kiểm tra dành cho giáo viên tiểu học Việt Nam.

Hãy tạo một đề kiểm tra hoàn chỉnh dựa trên thông tin sau:

- Khối lớp: {$grade}
- Môn học: {$subject}
- Bài học / chủ đề: {$lessonName}
- Thời lượng: {$duration}
- Số câu hỏi: {$numQuestions}
- Độ khó: {$difficulty}
- Hình thức: {$formatDescription}

YÊU CẦU:

1. Nội dung phù hợp với học sinh lớp {$grade}.
2. Bám sát môn {$subject}.
3. Bám sát chủ đề "{$lessonName}".
4. Không đưa kiến thức quá cao.
5. Không tạo câu hỏi trùng nhau.
6. Câu hỏi rõ ràng, dễ hiểu.
7. Nếu trắc nghiệm phải có A, B, C, D.
8. Chỉ có một đáp án đúng.
9. Nếu tự luận phải có hướng dẫn chấm.
10. Cuối đề có phần ĐÁP ÁN VÀ HƯỚNG DẪN CHẤM.
11. Có họ tên học sinh, lớp, ngày kiểm tra.
12. Có thể sử dụng HTML cơ bản.

QUAN TRỌNG:

- Chỉ trả về HTML.
- Không Markdown.
- Không dùng ```html.
- Không giải thích bên ngoài đề.
- Không dùng JavaScript.
- Không dùng PHP.
- Không tạo html/head/body.
- Chỉ trả về một <div>.

Cấu trúc:

<div class="quiz-content">

    <h2>...</h2>

    <p><strong>Họ và tên:</strong> ....................................</p>
    <p><strong>Lớp:</strong> ....................</p>
    <p><strong>Ngày kiểm tra:</strong> ....................</p>

    <hr>

    <h3>PHẦN I</h3>

    <div class="question">
        <p><strong>Câu 1.</strong> ...</p>
        <p>A. ...</p>
        <p>B. ...</p>
        <p>C. ...</p>
        <p>D. ...</p>
    </div>

    <hr>

    <h3>ĐÁP ÁN VÀ HƯỚNG DẪN CHẤM</h3>

    <ol>
        <li>...</li>
    </ol>

</div>
PROMPT;

            if (!defined('GROQ_API_KEY') || trim(GROQ_API_KEY) === '') {
                throw new Exception(
                    'Chưa cấu hình GROQ_API_KEY trong config/constants.php.'
                );
            }

            if (GROQ_API_KEY === 'YOUR_NEW_GROQ_API_KEY') {
                throw new Exception(
                    'Bạn chưa thay GROQ_API_KEY bằng API key mới.'
                );
            }

            $payload = [
                'model' => GROQ_PRIMARY_MODEL,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' =>
                            'Bạn là AI chuyên hỗ trợ giáo viên tiểu học Việt Nam tạo đề kiểm tra.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_completion_tokens' => 5000,
                'stream' => false,
                'include_reasoning' => false
            ];

            $ch = curl_init(GROQ_API_URL);

            if ($ch === false) {
                throw new Exception(
                    'Không thể khởi tạo kết nối đến Groq API.'
                );
            }

            curl_setopt_array(
                $ch,
                [
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . GROQ_API_KEY,
                        'Accept: application/json'
                    ],
                    CURLOPT_POSTFIELDS => json_encode(
                        $payload,
                        JSON_UNESCAPED_UNICODE
                    ),
                    CURLOPT_TIMEOUT => 120,
                    CURLOPT_CONNECTTIMEOUT => 20
                ]
            );

            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            $httpCode = curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );

            curl_close($ch);

            if ($response === false) {
                throw new Exception(
                    'Không thể kết nối Groq API: ' . $curlError
                );
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                $errorData = json_decode(
                    $response,
                    true
                );

                $errorMessage = 'Không xác định';

                if (
                    is_array($errorData) &&
                    isset($errorData['error']['message'])
                ) {
                    $errorMessage =
                        $errorData['error']['message'];
                } elseif (trim($response) !== '') {
                    $errorMessage = $response;
                }

                throw new Exception(
                    'Groq API HTTP ' .
                    $httpCode .
                    ': ' .
                    $errorMessage
                );
            }

            $data = json_decode(
                $response,
                true
            );

            if (!is_array($data)) {
                throw new Exception(
                    'Groq trả về JSON không hợp lệ.'
                );
            }

            $content = '';

            if (
                isset($data['choices'][0]['message']['content'])
            ) {
                $content =
                    $data['choices'][0]['message']['content'];
            }

            $content = trim($content);

            if ($content === '') {
                throw new Exception(
                    'AI không trả về nội dung đề kiểm tra.'
                );
            }

            $content = preg_replace(
                '/^```(?:html)?\s*/i',
                '',
                $content
            );

            $content = preg_replace(
                '/\s*```$/',
                '',
                $content
            );

            $content = trim($content);

            $pdo = $this->getDB();

            $sql = "
                INSERT INTO quizzes
                (
                    teacher_id,
                    grade,
                    subject,
                    lesson_name,
                    duration,
                    format,
                    content,
                    created_at
                )
                VALUES
                (
                    :teacher_id,
                    :grade,
                    :subject,
                    :lesson_name,
                    :duration,
                    :format,
                    :content,
                    NOW()
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':teacher_id' => $teacherId,
                ':grade' => (int)$grade,
                ':subject' => $subject,
                ':lesson_name' => $lessonName,
                ':duration' => $duration,
                ':format' => $format,
                ':content' => $content
            ]);

            $quizId = (int)$pdo->lastInsertId();

            echo json_encode(
                [
                    'success' => true,
                    'message' => 'Tạo đề kiểm tra thành công.',
                    'quiz_id' => $quizId,
                    'redirect' =>
                        BASE_URL . '/quiz/detail/' . $quizId
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit;

        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode(
                [
                    'success' => false,
                    'message' => 'Không thể tạo đề kiểm tra.',
                    'detail' => $e->getMessage()
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | XEM CHI TIẾT
    |--------------------------------------------------------------------------
    */

    public function detail($id = null)
    {
        $this->requireLogin();

        if ($id === null || !is_numeric($id)) {
            http_response_code(404);
            die('Không tìm thấy đề kiểm tra.');
        }

        $quizId = (int)$id;

        if ($quizId <= 0) {
            http_response_code(404);
            die('ID đề kiểm tra không hợp lệ.');
        }

        $pdo = $this->getDB();

        $sql = "
            SELECT
                q.id,
                q.teacher_id,
                q.grade,
                q.subject,
                q.lesson_name,
                q.duration,
                q.format,
                q.content,
                q.created_at,
                IFNULL(u.email, '') AS teacher_username,
                IFNULL(u.name, 'Giáo viên') AS teacher_name
            FROM quizzes q
            LEFT JOIN users u
                ON q.teacher_id = u.id
            WHERE q.id = :id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id' => $quizId
        ]);

        $quiz = $stmt->fetch();

        if (!$quiz) {
            http_response_code(404);
            die('Đề kiểm tra không tồn tại.');
        }

        require ROOT_PATH . '/app/Views/quizzes/detail.php';
    }

    /*
    |--------------------------------------------------------------------------
    | TRANG SỬA ĐỀ
    |--------------------------------------------------------------------------
    */

    public function edit($id = null)
    {
        $this->requireTeacher();

        if ($id === null || !is_numeric($id)) {
            http_response_code(404);
            die('ID đề kiểm tra không hợp lệ.');
        }

        $quizId = (int)$id;

        $teacherId = $this->getTeacherId();

        $pdo = $this->getDB();

        $sql = "
            SELECT
                id,
                teacher_id,
                grade,
                subject,
                lesson_name,
                duration,
                format,
                content,
                created_at
            FROM quizzes
            WHERE id = :id
              AND teacher_id = :teacher_id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id' => $quizId,
            ':teacher_id' => $teacherId
        ]);

        $quiz = $stmt->fetch();

        if (!$quiz) {
            http_response_code(404);
            die('Không tìm thấy đề hoặc bạn không có quyền sửa đề này.');
        }

        require ROOT_PATH . '/app/Views/quizzes/edit.php';
    }

    /*
    |--------------------------------------------------------------------------
    | CẬP NHẬT ĐỀ
    |--------------------------------------------------------------------------
    */

    public function update($id = null)
    {
        $this->requireTeacher();

        if ($id === null || !is_numeric($id)) {
            http_response_code(404);
            die('ID đề kiểm tra không hợp lệ.');
        }

        $quizId = (int)$id;

        $teacherId = $this->getTeacherId();

        $grade = isset($_POST['grade'])
            ? trim($_POST['grade'])
            : '';

        $subject = isset($_POST['subject'])
            ? trim($_POST['subject'])
            : '';

        $lessonName = isset($_POST['lesson_name'])
            ? trim($_POST['lesson_name'])
            : '';

        $duration = isset($_POST['duration'])
            ? trim($_POST['duration'])
            : '';

        $format = isset($_POST['format'])
            ? trim($_POST['format'])
            : '';

        $content = isset($_POST['content'])
            ? trim($_POST['content'])
            : '';

        if ($grade === '') {
            die('Vui lòng chọn lớp.');
        }

        if ($subject === '') {
            die('Vui lòng nhập môn học.');
        }

        if ($lessonName === '') {
            die('Vui lòng nhập tên bài học.');
        }

        if ($duration === '') {
            die('Vui lòng chọn thời lượng.');
        }

        if ($format === '') {
            die('Vui lòng chọn hình thức.');
        }

        if ($content === '') {
            die('Nội dung đề không được để trống.');
        }

        $pdo = $this->getDB();

        /*
         * Kiểm tra đề có thuộc giáo viên hiện tại không
         */
        $checkSql = "
            SELECT id
            FROM quizzes
            WHERE id = :id
              AND teacher_id = :teacher_id
            LIMIT 1
        ";

        $checkStmt = $pdo->prepare($checkSql);

        $checkStmt->execute([
            ':id' => $quizId,
            ':teacher_id' => $teacherId
        ]);

        $quiz = $checkStmt->fetch();

        if (!$quiz) {
            http_response_code(403);
            die('Bạn không có quyền sửa đề này.');
        }

        /*
         * Cập nhật
         */
        $sql = "
            UPDATE quizzes
            SET
                grade = :grade,
                subject = :subject,
                lesson_name = :lesson_name,
                duration = :duration,
                format = :format,
                content = :content
            WHERE id = :id
              AND teacher_id = :teacher_id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':grade' => (int)$grade,
            ':subject' => $subject,
            ':lesson_name' => $lessonName,
            ':duration' => $duration,
            ':format' => $format,
            ':content' => $content,
            ':id' => $quizId,
            ':teacher_id' => $teacherId
        ]);

        header(
            'Location: ' .
            BASE_URL .
            '/quiz/detail/' .
            $quizId
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | XÓA ĐỀ
    |--------------------------------------------------------------------------
    */

    public function delete($id = null)
    {
        $this->requireTeacher();

        if ($id === null || !is_numeric($id)) {
            http_response_code(404);
            die('ID đề kiểm tra không hợp lệ.');
        }

        $quizId = (int)$id;

        $teacherId = $this->getTeacherId();

        $pdo = $this->getDB();

        /*
         * Kiểm tra quyền sở hữu
         */
        $checkSql = "
            SELECT id
            FROM quizzes
            WHERE id = :id
              AND teacher_id = :teacher_id
            LIMIT 1
        ";

        $checkStmt = $pdo->prepare($checkSql);

        $checkStmt->execute([
            ':id' => $quizId,
            ':teacher_id' => $teacherId
        ]);

        $quiz = $checkStmt->fetch();

        if (!$quiz) {
            http_response_code(403);
            die('Bạn không có quyền xóa đề này.');
        }

        /*
         * Xóa
         */
        $deleteSql = "
            DELETE FROM quizzes
            WHERE id = :id
              AND teacher_id = :teacher_id
        ";

        $deleteStmt = $pdo->prepare($deleteSql);

        $deleteStmt->execute([
            ':id' => $quizId,
            ':teacher_id' => $teacherId
        ]);

        header(
            'Location: ' .
            BASE_URL .
            '/quiz/index'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | API DETAIL
    |--------------------------------------------------------------------------
    */

    public function apiDetail($id = null)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $this->requireLogin();

            if ($id === null || !is_numeric($id)) {
                throw new Exception(
                    'ID đề kiểm tra không hợp lệ.'
                );
            }

            $quizId = (int)$id;

            $pdo = $this->getDB();

            $sql = "
                SELECT
                    q.id,
                    q.teacher_id,
                    q.grade,
                    q.subject,
                    q.lesson_name,
                    q.duration,
                    q.format,
                    q.content,
                    q.created_at,
                    IFNULL(u.name, 'Giáo viên') AS teacher_name
                FROM quizzes q
                LEFT JOIN users u
                    ON q.teacher_id = u.id
                WHERE q.id = :id
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':id' => $quizId
            ]);

            $quiz = $stmt->fetch();

            if (!$quiz) {
                throw new Exception(
                    'Không tìm thấy đề kiểm tra.'
                );
            }

            echo json_encode(
                [
                    'success' => true,
                    'quiz' => $quiz
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit;

        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit;
        }
    }
}

