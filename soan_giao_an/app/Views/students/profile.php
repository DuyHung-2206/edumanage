<?php require_once ROOT_PATH . '/app/Views/includes/header.php'; ?>
<?php require_once ROOT_PATH . '/app/Views/includes/navbar.php'; ?>

<?php
$sName = $student['full_name'] ?? 'Học sinh';
$initial = mb_substr(trim($sName), 0, 1, 'UTF-8');
$backUrl = ($currentUser && $currentUser['role'] === 'teacher') ? BASE_URL . '/student/index' : BASE_URL . '/quiz/index';
?>

<div class="max-w-3xl mx-auto space-y-6 pb-12">
    
    <!-- BREADCRUMB -->
    <div>
        <a href="<?= $backUrl ?>"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition group">
            <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            <span><?= ($currentUser && $currentUser['role'] === 'teacher') ? 'Quay lại danh sách lớp' : 'Quay lại trang chủ' ?></span>
        </a>
    </div>

    <!-- PROFILE CARD -->
    <div class="glass-panel rounded-3xl p-6 sm:p-10 space-y-8 border border-slate-800 shadow-2xl relative overflow-hidden">
        
        <!-- Background Aura -->
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- USER HEADER -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 font-black text-3xl flex items-center justify-center shadow-xl shadow-emerald-500/25 flex-shrink-0">
                <?= htmlspecialchars(mb_strtoupper($initial, 'UTF-8')) ?>
            </div>
            
            <div class="space-y-1.5 flex-1">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-[11px] font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span>Hồ Sơ Học Sinh Tiểu Học</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    <?= htmlspecialchars($sName) ?>
                </h1>
                <p class="text-xs text-slate-400">
                    Tên đăng nhập hệ thống: <strong class="text-indigo-400 font-mono">@<?= htmlspecialchars($student['username']) ?></strong>
                </p>
            </div>
        </div>

        <!-- INFO GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-800/80">
            
            <!-- Lớp học -->
            <div class="glass-card rounded-2xl p-4 space-y-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-chalkboard text-indigo-400"></i>
                    <span>Lớp Học</span>
                </div>
                <div class="text-base font-extrabold text-white">
                    <?= htmlspecialchars($student['class_name'] ?? 'Chưa xếp lớp') ?>
                </div>
            </div>

            <!-- Khối Lớp -->
            <div class="glass-card rounded-2xl p-4 space-y-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-graduation-cap text-amber-400"></i>
                    <span>Khối Lớp</span>
                </div>
                <div class="text-base font-extrabold text-white">
                    <?= !empty($student['grade_level']) ? 'Lớp ' . htmlspecialchars($student['grade_level']) : 'Khối Tiểu học' ?>
                </div>
            </div>

            <!-- Giáo viên phụ trách -->
            <div class="glass-card rounded-2xl p-4 space-y-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-chalkboard-user text-emerald-400"></i>
                    <span>Giáo Viên Phụ Trách</span>
                </div>
                <div class="text-base font-extrabold text-white truncate" title="<?= htmlspecialchars($student['teacher_name'] ?? '') ?>">
                    <?= htmlspecialchars($student['teacher_name'] ?? 'Chưa phân công') ?>
                </div>
            </div>

        </div>

        <!-- GHI CHÚ / NHẬN XÉT -->
        <div class="space-y-3 pt-2">
            <h3 class="text-xs font-extrabold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-amber-400"></i>
                <span>Ghi Chú Đánh Giá & Nhận Xét Của Giáo Viên</span>
            </h3>

            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 text-xs text-slate-300 leading-relaxed min-h-[100px] relative">
                <?php if (!empty($student['notes'])): ?>
                    <i class="fa-solid fa-quote-left text-slate-700 text-2xl absolute top-3 left-3 opacity-30"></i>
                    <div class="relative z-10 pl-5 italic font-medium">
                        <?= nl2br(htmlspecialchars($student['notes'])) ?>
                    </div>
                <?php else: ?>
                    <div class="text-slate-500 italic flex items-center gap-2 h-full justify-center">
                        <i class="fa-regular fa-comment-dots text-lg"></i>
                        <span>Hiện chưa có nhận xét hay ghi chú nào từ giáo viên phụ trách.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
            <a href="<?= BASE_URL ?>/quiz/index"
               class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-indigo-600/20">
                <i class="fa-solid fa-file-pen text-[11px]"></i>
                <span>Xem Đề Thi Của Lớp</span>
            </a>

            <button type="button" onclick="window.print()"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-print text-[11px]"></i>
                <span>In hồ sơ</span>
            </button>
        </div>

    </div>

</div>

<?php require_once ROOT_PATH . '/app/Views/includes/footer.php'; ?>