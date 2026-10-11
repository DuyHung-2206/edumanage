<?php
require_once __DIR__.'/../config/config.php';

$u = require_role(['teacher']);
$sid = (int)($_POST['student_id'] ?? 0);
$month = $_POST['month_key'] ?? '';

own_student($sid, $u['id']);

if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    json_response([
        'success' => false,
        'message' => 'Tháng không hợp lệ.'
    ], 422);
}

/*
 * Nhận xét chung
 */
$overview = trim($_POST['overview'] ?? '');

/*
 * Nhận xét theo môn.
 * Form gửi:
 *
 * subject_feedback[Toán]
 * subject_feedback[Tiếng Việt]
 *
 * subject_roadmap[Toán]
 * subject_roadmap[Tiếng Việt]
 */
$subjectFeedback = $_POST['subject_feedback'] ?? [];
$subjectRoadmap  = $_POST['subject_roadmap'] ?? [];

if (!is_array($subjectFeedback)) {
    $subjectFeedback = [];
}

if (!is_array($subjectRoadmap)) {
    $subjectRoadmap = [];
}

/*
 * Chỉ giữ dữ liệu dạng:
 * "Tên môn" => "Nội dung"
 */
$cleanFeedback = [];

foreach ($subjectFeedback as $subject => $text) {
    $subject = trim((string)$subject);
    $text = trim((string)$text);

    if ($subject !== '' && $text !== '') {
        $cleanFeedback[$subject] = $text;
    }
}

$cleanRoadmap = [];

foreach ($subjectRoadmap as $subject => $text) {
    $subject = trim((string)$subject);
    $text = trim((string)$text);

    if ($subject !== '' && $text !== '') {
        $cleanRoadmap[$subject] = $text;
    }
}

/*
 * Giữ các field cũ để tương thích dữ liệu cũ.
 * Không tự động ghi đè algebra/geometry.
 */
$oldAlgebra = trim($_POST['algebra'] ?? '');
$oldGeometry = trim($_POST['geometry'] ?? '');
$oldNextAlgebra = trim($_POST['next_algebra'] ?? '');
$oldNextGeometry = trim($_POST['next_geometry'] ?? '');

$feedbackJson = json_encode(
    $cleanFeedback,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);

$roadmapJson = json_encode(
    $cleanRoadmap,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);

$st = db()->prepare(
    'INSERT INTO feedbacks
    (
        student_id,
        month_key,
        overview,
        algebra,
        geometry,
        next_algebra,
        next_geometry,
        subject_feedback,
        subject_roadmap
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        overview = VALUES(overview),
        algebra = VALUES(algebra),
        geometry = VALUES(geometry),
        next_algebra = VALUES(next_algebra),
        next_geometry = VALUES(next_geometry),
        subject_feedback = VALUES(subject_feedback),
        subject_roadmap = VALUES(subject_roadmap)'
);

$st->execute([
    $sid,
    $month,
    $overview,
    $oldAlgebra,
    $oldGeometry,
    $oldNextAlgebra,
    $oldNextGeometry,
    $feedbackJson,
    $roadmapJson
]);

json_response([
    'success' => true
]);
