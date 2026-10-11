<?php
$pageTitle = 'Chỉnh Sửa Đề Kiểm Tra';
require_once ROOT_PATH . '/app/Views/includes/header.php';
require_once ROOT_PATH . '/app/Views/includes/navbar.php';

$quizId = (int)($quiz['id'] ?? 0);
$grade = $quiz['grade'] ?? 1;
$subject = $quiz['subject'] ?? '';
$lessonName = $quiz['lesson_name'] ?? '';
$duration = $quiz['duration'] ?? '15p';
$format = $quiz['format'] ?? 'mixed';
$content = $quiz['content'] ?? '';
?>

<div class="max-w-6xl mx-auto space-y-6 pb-12">

    <!-- BREADCRUMB & HEADER -->
    <div class="space-y-3">
        <a href="<?= BASE_URL ?>/quiz/detail/<?= $quizId ?>"
           class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition group">
            <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            <span>Quay lại xem đề</span>
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                    <i class="fa-solid fa-pen-to-square text-amber-500"></i>
                    <span>Chỉnh Sửa Đề Kiểm Tra & Giáo Án</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Cập nhật lại thông tin bài kiểm tra hoặc chỉnh sửa trực tiếp nội dung câu hỏi và đáp án.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?= BASE_URL ?>/quiz/detail/<?= $quizId ?>"
                   class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Hủy bỏ
                </a>
                <button type="submit" form="editQuizForm"
                        class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-md shadow-blue-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Lưu Thay Đổi</span>
                </button>
            </div>
        </div>
    </div>

    <!-- EDIT FORM (DUAL PANE: FORM & LIVE PREVIEW) -->
    <form id="editQuizForm" method="POST" action="<?= BASE_URL ?>/quiz/update/<?= $quizId ?>" class="space-y-6">

        <!-- METADATA CARD -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 space-y-5 shadow-sm">
            <h2 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-sliders text-blue-600"></i>
                <span>Thông tin Đề thi</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Khối Lớp -->
                <div>
                    <label for="grade" class="block text-xs font-bold text-slate-700 mb-1">Khối Lớp</label>
                    <select id="grade" name="grade" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= (int)$grade === $i ? 'selected' : '' ?>>Lớp <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Môn học -->
                <div>
                    <label for="subject" class="block text-xs font-bold text-slate-700 mb-1">Môn học</label>
                    <input type="text" id="subject" name="subject" required value="<?= htmlspecialchars($subject) ?>"
                           class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <!-- Thời lượng -->
                <div>
                    <label for="duration" class="block text-xs font-bold text-slate-700 mb-1">Thời lượng</label>
                    <select id="duration" name="duration" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        <option value="15p" <?= $duration === '15p' ? 'selected' : '' ?>>15 phút</option>
                        <option value="45p" <?= $duration === '45p' ? 'selected' : '' ?>>45 phút</option>
                    </select>
                </div>

                <!-- Hình thức -->
                <div>
                    <label for="format" class="block text-xs font-bold text-slate-700 mb-1">Hình thức</label>
                    <select id="format" name="format" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        <option value="mixed" <?= $format === 'mixed' ? 'selected' : '' ?>>Kết hợp</option>
                        <option value="multiple_choice" <?= $format === 'multiple_choice' ? 'selected' : '' ?>>Trắc nghiệm</option>
                        <option value="essay" <?= $format === 'essay' ? 'selected' : '' ?>>Tự luận</option>
                    </select>
                </div>
            </div>

            <!-- Tên bài học -->
            <div>
                <label for="lesson_name" class="block text-xs font-bold text-slate-700 mb-1">Tên Bài học / Chủ đề kiểm tra</label>
                <input type="text" id="lesson_name" name="lesson_name" required value="<?= htmlspecialchars($lessonName) ?>"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
            </div>
        </div>

        <!-- CONTENT EDITOR & LIVE PREVIEW (2 TABS ON MOBILE, 2 COLUMNS ON DESKTOP) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 space-y-4 shadow-sm">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <h2 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider">
                        <i class="fa-solid fa-code text-blue-600 mr-1"></i> Nội dung Đề thi (HTML / Văn bản)
                    </h2>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-blue-600 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>Live Preview tự động</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Left: Text Editor -->
                <div class="space-y-2">
                    <div class="text-[11px] font-bold text-slate-400 flex items-center justify-between">
                        <span>Khung soạn thảo mã nguồn</span>
                    </div>
                    <textarea id="content" name="content" required rows="22"
                              class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 transition leading-relaxed resize-y"><?= htmlspecialchars($content) ?></textarea>
                </div>

                <!-- Right: Live Preview Box -->
                <div class="space-y-2">
                    <div class="text-[11px] font-bold text-slate-400">
                        <span>Xem trước kết quả hiển thị</span>
                    </div>
                    <div class="bg-slate-50 text-slate-900 rounded-2xl p-6 border border-slate-200 overflow-y-auto max-h-[520px] shadow-inner text-sm leading-relaxed font-serif" id="livePreview">
                        <?= $content ?>
                    </div>
                </div>

            </div>

        </div>

        <!-- BOTTOM ACTIONS -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="<?= BASE_URL ?>/quiz/detail/<?= $quizId ?>"
               class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Hủy bỏ
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold transition shadow-md shadow-blue-500/20 flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Cập nhật Đề kiểm tra</span>
            </button>
        </div>

    </form>

</div>

<script>
// Live preview update
document.addEventListener('DOMContentLoaded', () => {
    const editor = document.getElementById('content');
    const preview = document.getElementById('livePreview');

    if (editor && preview) {
        editor.addEventListener('input', () => {
            preview.innerHTML = editor.value;
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/app/Views/includes/footer.php'; ?>
