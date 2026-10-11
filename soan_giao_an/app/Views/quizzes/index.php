<?php 
$pageTitle = 'Kho Giáo Án & Đề Thi AI';
require_once ROOT_PATH . '/app/Views/includes/header.php';
require_once ROOT_PATH . '/app/Views/includes/navbar.php';

$totalQuizzes = count($quizzes ?? []);
$gradeCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
if (!empty($quizzes)) {
    foreach ($quizzes as $q) {
        $g = (int)($q['grade'] ?? 0);
        if (isset($gradeCounts[$g])) {
            $gradeCounts[$g]++;
        }
    }
}
?>

<div class="space-y-6">

    <!-- PAGE HERO & HEADER (BLUE & WHITE) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold">
                <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                <span>Trợ lý Khảo thí & Soạn Giáo Án AI</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                <?= ($currentUser['role'] === 'teacher') ? 'Kho Giáo Án & Đề Kiểm Tra' : 'Đề Kiểm Tra Của Lớp Bạn' ?>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-2xl">
                Biên soạn đề kiểm tra 15 phút, 1 tiết và giáo án chuẩn chương trình Giáo dục Tiểu học mới (Lớp 1 đến Lớp 5) tích hợp AI Groq.
            </p>
        </div>

        <?php if ($currentUser['role'] === 'teacher'): ?>
            <div class="flex-shrink-0">
                <a href="<?= BASE_URL ?>/quiz/create"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fa-solid fa-sparkles text-amber-300"></i>
                    <span>Soạn Đề Mới Bằng AI</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- OVERVIEW STATS -->
    <?php if (!empty($quizzes)): ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Tổng đề -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold flex-shrink-0">
                <i class="fa-solid fa-folder-tree"></i>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tổng số đề</div>
                <div class="text-xl font-extrabold text-slate-800"><?= $totalQuizzes ?></div>
            </div>
        </div>

        <!-- Thống kê lớp 1-5 -->
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <button type="button" onclick="quickFilterGrade('<?= $i ?>')" class="bg-white rounded-2xl border border-slate-200/90 p-4 flex items-center gap-3 text-left hover:border-blue-400 hover:shadow-sm transition-all shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 font-extrabold text-sm flex items-center justify-center flex-shrink-0">
                    K<?= $i ?>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400">Khối Lớp <?= $i ?></div>
                    <div class="text-lg font-extrabold text-slate-700"><?= $gradeCounts[$i] ?> <span class="text-[11px] text-slate-400 font-medium">đề</span></div>
                </div>
            </button>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <!-- SEARCH & FILTER TOOLBAR -->
    <?php if (!empty($quizzes)): ?>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
        
        <!-- Search Input -->
        <div class="relative flex-1 max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo môn học, tên bài học..."
                   class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
        </div>

        <!-- Grade Pills Filter -->
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-[11px] font-bold text-slate-400 mr-1 hidden sm:inline uppercase">Khối:</span>
            <button type="button" data-grade="all" class="grade-filter-btn px-3 py-1.5 rounded-xl text-xs font-bold transition bg-blue-600 text-white shadow-sm">
                Tất cả
            </button>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <button type="button" data-grade="<?= $i ?>" class="grade-filter-btn px-3 py-1.5 rounded-xl text-xs font-bold transition bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800 border border-slate-200/60">
                    Lớp <?= $i ?>
                </button>
            <?php endfor; ?>
        </div>

        <!-- Format Dropdown Filter -->
        <div class="flex items-center gap-2">
            <select id="formatFilter" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500">
                <option value="all">Tất cả hình thức</option>
                <option value="multiple_choice">Trắc nghiệm</option>
                <option value="essay">Tự luận</option>
                <option value="mixed">Trắc nghiệm + Tự luận</option>
            </select>

            <!-- Reset Button -->
            <button type="button" id="resetFilterBtn" title="Đặt lại bộ lọc" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs border border-slate-200 transition">
                <i class="fa-solid fa-rotate-left"></i>
            </button>
        </div>

    </div>
    <?php endif; ?>

    <!-- DANH SÁCH ĐỀ HOẶC EMPTY STATE -->
    <?php if (empty($quizzes)): ?>
        <div class="bg-white rounded-3xl p-12 text-center space-y-4 max-w-lg mx-auto my-8 border border-slate-200 shadow-sm">
            <div class="w-20 h-20 rounded-3xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center mx-auto text-3xl shadow-xs">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-bold text-slate-800">Chưa có đề kiểm tra nào</h3>
                <p class="text-xs text-slate-400">
                    <?= ($currentUser['role'] === 'teacher') 
                        ? 'Hãy khởi tạo giáo án hoặc đề thi đầu tiên với sự trợ giúp của AI thông minh trong vài giây.' 
                        : 'Giáo viên phụ trách lớp của bạn hiện chưa đăng tải đề kiểm tra nào.' 
                    ?>
                </p>
            </div>
            <?php if ($currentUser['role'] === 'teacher'): ?>
                <div class="pt-2">
                    <a href="<?= BASE_URL ?>/quiz/create" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-md shadow-blue-500/20">
                        <i class="fa-solid fa-plus"></i>
                        <span>Bắt đầu tạo đề ngay</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>

        <!-- GRID DANH SÁCH ĐỀ (WHITE CARDS) -->
        <div id="quizGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($quizzes as $q): ?>
                <?php
                $subjectName = trim($q['subject'] ?? '');
                $subjectLower = mb_strtolower($subjectName, 'UTF-8');
                
                // Phối màu badge theo môn học
                $subjectBg = 'bg-slate-100 text-slate-700 border-slate-200';
                if (str_contains($subjectLower, 'toán')) {
                    $subjectBg = 'bg-blue-50 text-blue-700 border-blue-200';
                } elseif (str_contains($subjectLower, 'việt') || str_contains($subjectLower, 'văn')) {
                    $subjectBg = 'bg-rose-50 text-rose-700 border-rose-200';
                } elseif (str_contains($subjectLower, 'anh')) {
                    $subjectBg = 'bg-purple-50 text-purple-700 border-purple-200';
                } elseif (str_contains($subjectLower, 'khoa') || str_contains($subjectLower, 'nhiên')) {
                    $subjectBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                } elseif (str_contains($subjectLower, 'sử') || str_contains($subjectLower, 'địa')) {
                    $subjectBg = 'bg-amber-50 text-amber-700 border-amber-200';
                }
                
                $formatLabel = 'Trắc nghiệm + Tự luận';
                $formatIcon = 'fa-cubes';
                if ($q['format'] === 'multiple_choice') {
                    $formatLabel = 'Trắc nghiệm';
                    $formatIcon = 'fa-list-check';
                } elseif ($q['format'] === 'essay') {
                    $formatLabel = 'Tự luận';
                    $formatIcon = 'fa-pen-clip';
                }
                ?>
                <div class="quiz-item bg-white rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all p-5 flex flex-col justify-between space-y-4 shadow-sm"
                     data-grade="<?= htmlspecialchars($q['grade']) ?>"
                     data-format="<?= htmlspecialchars($q['format']) ?>"
                     data-subject="<?= htmlspecialchars($subjectLower) ?>"
                     data-lesson="<?= htmlspecialchars(mb_strtolower($q['lesson_name'], 'UTF-8')) ?>">
                    
                    <!-- Header Card -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <!-- Khối Lớp -->
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                    Lớp <?= htmlspecialchars($q['grade']) ?>
                                </span>

                                <!-- Môn học -->
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase border <?= $subjectBg ?>">
                                    <?= htmlspecialchars($subjectName) ?>
                                </span>
                            </div>

                            <!-- Thời lượng -->
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                <i class="fa-regular fa-clock text-[9px] text-amber-500"></i>
                                <?= htmlspecialchars($q['duration']) ?>
                            </span>
                        </div>

                        <!-- Tên bài học -->
                        <div>
                            <h3 class="text-base font-bold text-slate-800 hover:text-blue-600 transition-colors line-clamp-2 leading-snug">
                                <a href="<?= BASE_URL ?>/quiz/detail/<?= (int)$q['id'] ?>">
                                    <?= htmlspecialchars($q['lesson_name']) ?>
                                </a>
                            </h3>
                            <div class="flex items-center gap-2 mt-2 text-[11px] text-slate-400">
                                <span class="inline-flex items-center gap-1 font-medium text-slate-500">
                                    <i class="fa-solid <?= $formatIcon ?> text-slate-400"></i>
                                    <?= $formatLabel ?>
                                </span>
                                <?php if (!empty($q['teacher_name'])): ?>
                                    <span>•</span>
                                    <span class="truncate max-w-[120px] font-medium" title="Giáo viên: <?= htmlspecialchars($q['teacher_name']) ?>">
                                        GV: <?= htmlspecialchars($q['teacher_name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Card -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="text-[10px] text-slate-400 flex items-center gap-1 font-medium">
                            <i class="fa-regular fa-calendar"></i>
                            <?= date('d/m/Y', strtotime($q['created_at'])) ?>
                        </span>

                        <div class="flex items-center gap-1.5">
                            <!-- Nút Xem -->
                            <a href="<?= BASE_URL ?>/quiz/detail/<?= (int)$q['id'] ?>"
                               class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white text-xs font-bold transition-all flex items-center gap-1 border border-blue-200/70 shadow-2xs">
                                <i class="fa-solid fa-eye text-[11px]"></i>
                                <span>Xem</span>
                            </a>

                            <?php if ($currentUser['role'] === 'teacher'): ?>
                                <!-- Nút Sửa -->
                                <a href="<?= BASE_URL ?>/quiz/edit/<?= (int)$q['id'] ?>"
                                   class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-600 text-amber-600 hover:text-white text-xs font-bold transition-all flex items-center gap-1 border border-amber-200/70"
                                   title="Chỉnh sửa đề">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                </a>

                                <!-- Nút Xóa -->
                                <button type="button"
                                        onclick="confirmDelete(<?= (int)$q['id'] ?>, '<?= htmlspecialchars(addslashes($q['lesson_name'])) ?>')"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white text-xs font-bold transition-all flex items-center gap-1 border border-rose-200/70"
                                        title="Xóa đề này">
                                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Fallback when Filtering -->
        <div id="noResultsMsg" class="hidden bg-white rounded-3xl p-10 text-center space-y-3 max-w-md mx-auto border border-slate-200 shadow-sm">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-filter-circle-xmark"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-700">Không tìm thấy đề nào phù hợp</h4>
            <p class="text-xs text-slate-400">Hãy thử thay đổi từ khóa hoặc bộ lọc lớp học của bạn.</p>
        </div>

    <?php endif; ?>

</div>

<!-- DELETE CONFIRMATION MODAL -->
<div id="deleteModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-200 shadow-2xl space-y-5 transform transition-all">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Xác nhận xóa đề kiểm tra?</h3>
                <p class="text-xs text-slate-500">Hành động này không thể hoàn tác sau khi thực hiện.</p>
            </div>
        </div>

        <p class="text-xs text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-200/80">
            Bạn có chắc chắn muốn xóa đề: <strong id="deleteQuizTitle" class="text-slate-800 font-bold"></strong>?
        </p>

        <div class="flex items-center justify-end gap-2.5 pt-2">
            <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Hủy bỏ
            </button>
            <a id="deleteConfirmBtn" href="#"
               class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-md shadow-rose-600/25">
                Xóa vĩnh viễn
            </a>
        </div>
    </div>
</div>

<script>
// Filter & Search Logic
let currentGrade = 'all';
let currentFormat = 'all';
let currentKeyword = '';

function applyFilters() {
    const items = document.querySelectorAll('.quiz-item');
    let visibleCount = 0;

    items.forEach(item => {
        const itemGrade = item.getAttribute('data-grade');
        const itemFormat = item.getAttribute('data-format');
        const itemSubject = item.getAttribute('data-subject');
        const itemLesson = item.getAttribute('data-lesson');

        const matchGrade = (currentGrade === 'all' || itemGrade === currentGrade);
        const matchFormat = (currentFormat === 'all' || itemFormat === currentFormat);
        const matchKeyword = (!currentKeyword || itemSubject.includes(currentKeyword) || itemLesson.includes(currentKeyword));

        if (matchGrade && matchFormat && matchKeyword) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    const noResults = document.getElementById('noResultsMsg');
    if (noResults) {
        noResults.style.display = (visibleCount === 0 && items.length > 0) ? 'block' : 'none';
    }
}

// Search input debounce
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        currentKeyword = e.target.value.toLowerCase().trim();
        applyFilters();
    });
}

// Grade filter buttons
const gradeBtns = document.querySelectorAll('.grade-filter-btn');
gradeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        gradeBtns.forEach(b => {
            b.classList.remove('bg-blue-600', 'text-white', 'shadow-sm');
            b.classList.add('bg-slate-100', 'text-slate-600', 'hover:bg-slate-200');
        });
        btn.classList.remove('bg-slate-100', 'text-slate-600', 'hover:bg-slate-200');
        btn.classList.add('bg-blue-600', 'text-white', 'shadow-sm');

        currentGrade = btn.getAttribute('data-grade');
        applyFilters();
    });
});

function quickFilterGrade(grade) {
    const targetBtn = document.querySelector(`.grade-filter-btn[data-grade="${grade}"]`);
    if (targetBtn) {
        targetBtn.click();
    }
}

// Format filter select
const formatFilter = document.getElementById('formatFilter');
if (formatFilter) {
    formatFilter.addEventListener('change', (e) => {
        currentFormat = e.target.value;
        applyFilters();
    });
}

// Reset filter
const resetBtn = document.getElementById('resetFilterBtn');
if (resetBtn) {
    resetBtn.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        currentKeyword = '';
        if (formatFilter) formatFilter.value = 'all';
        currentFormat = 'all';
        const allGradeBtn = document.querySelector('.grade-filter-btn[data-grade="all"]');
        if (allGradeBtn) allGradeBtn.click();
    });
}

// Delete modal
function confirmDelete(id, title) {
    const modal = document.getElementById('deleteModal');
    const titleEl = document.getElementById('deleteQuizTitle');
    const confirmBtn = document.getElementById('deleteConfirmBtn');

    if (titleEl) titleEl.innerText = title;
    if (confirmBtn) confirmBtn.href = '<?= BASE_URL ?>/quiz/delete/' + id;
    if (modal) modal.classList.remove('hidden');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) modal.classList.add('hidden');
}
</script>

<?php require_once ROOT_PATH . '/app/Views/includes/footer.php'; ?>
