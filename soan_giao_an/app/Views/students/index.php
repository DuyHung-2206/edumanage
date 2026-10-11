<?php require_once ROOT_PATH . '/app/Views/includes/header.php'; ?>
<?php require_once ROOT_PATH . '/app/Views/includes/navbar.php'; ?>

<?php
$totalStudents = count($students ?? []);
?>

<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-800/80 to-emerald-950/40 p-6 rounded-3xl border border-slate-800/80 shadow-xl">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-semibold">
                <i class="fa-solid fa-users text-emerald-400"></i>
                <span>Sổ Quản Lý Học Sinh</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Danh Sách Lớp & Hồ Sơ Học Sinh
            </h1>
            <p class="text-xs sm:text-sm text-slate-400">
                Quản lý thông tin học sinh, phân công lớp học và theo dõi ghi chú nhận xét kết quả học tập.
            </p>
        </div>

        <div class="flex-shrink-0">
            <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Học sinh quản lý</div>
                    <div class="text-base font-extrabold text-white"><?= $totalStudents ?> em</div>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM THÊM HỌC SINH -->
    <div class="glass-panel rounded-3xl p-6 sm:p-7 space-y-5 border border-slate-800">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h2 class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Thêm Học Sinh Vào Lớp</span>
            </h2>
            <span class="text-[11px] text-slate-400">Gán tài khoản có sẵn hoặc cấp mới</span>
        </div>

        <form action="<?= BASE_URL ?>/student/add" method="POST" class="space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Chọn học sinh đã đăng ký -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        Học sinh đã đăng ký tự do
                    </label>
                    <select name="existing_student_id" id="existingStudentSelect"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                        <option value="0">-- Hoặc tự tạo học sinh mới bên cạnh --</option>
                        <?php if (!empty($unassignedStudents)): ?>
                            <?php foreach ($unassignedStudents as $us): ?>
                                <option value="<?= $us['id'] ?>">
                                    <?= htmlspecialchars($us['full_name']) ?> (@<?= htmlspecialchars($us['username']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Họ tên nếu tạo mới -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        Họ và Tên (Nếu tạo mới)
                    </label>
                    <input type="text" name="full_name" placeholder="Ví dụ: Trần Minh Khang..."
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                </div>

                <!-- Khối Lớp -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        Khối Lớp
                    </label>
                    <select name="grade_level"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                        <option value="1">Lớp 1</option>
                        <option value="2">Lớp 2</option>
                        <option value="3" selected>Lớp 3</option>
                        <option value="4">Lớp 4</option>
                        <option value="5">Lớp 5</option>
                    </select>
                </div>

                <!-- Tên Lớp -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        Tên Lớp (Ví dụ: 3A, 3B...)
                    </label>
                    <input type="text" name="class_name" required value="Lớp 3A"
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                </div>

            </div>

            <!-- Ghi chú & Nút submit -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-1">
                <div class="md:col-span-3">
                    <input type="text" name="notes" placeholder="Ghi chú ban đầu (Học lực, hạnh kiểm, cần bồi dưỡng...)"
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                </div>
                <div class="md:col-span-1">
                    <button type="submit"
                            class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Thêm Vào Lớp</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- BẢNG DANH SÁCH HỌC SINH -->
    <div class="glass-panel rounded-3xl overflow-hidden border border-slate-800 shadow-xl">
        
        <!-- Toolbar tìm kiếm -->
        <div class="p-4 sm:p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-900/60">
            <h2 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-address-book text-emerald-400"></i>
                <span>Danh Sách Học Sinh Trong Lớp (<?= $totalStudents ?>)</span>
            </h2>

            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="studentSearch" placeholder="Lọc theo tên hoặc lớp..."
                       class="w-full pl-8 pr-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500 transition">
            </div>
        </div>

        <?php if (empty($students)): ?>
            <div class="p-12 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>
                <p class="text-sm text-slate-400">Chưa có học sinh nào trong danh sách quản lý của bạn.</p>
                <p class="text-xs text-slate-500">Hãy dùng biểu mẫu phía trên để thêm học sinh đầu tiên vào lớp.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="studentsTable">
                    <thead>
                        <tr class="bg-slate-900/80 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                            <th class="py-3.5 px-4">Học Sinh</th>
                            <th class="py-3.5 px-4">Tài Khoản</th>
                            <th class="py-3.5 px-4">Khối & Lớp</th>
                            <th class="py-3.5 px-4">Ghi Chú Nhận Xét</th>
                            <th class="py-3.5 px-4 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-xs">
                        <?php foreach ($students as $st): ?>
                            <?php
                            $sName = $st['full_name'] ?? 'Học sinh';
                            $initial = mb_substr(trim($sName), 0, 1, 'UTF-8');
                            ?>
                            <tr class="student-row hover:bg-slate-800/40 transition-colors"
                                data-name="<?= htmlspecialchars(mb_strtolower($sName, 'UTF-8')) ?>"
                                data-class="<?= htmlspecialchars(mb_strtolower($st['class_name'] ?? '', 'UTF-8')) ?>">
                                
                                <!-- Học sinh & Avatar -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold text-xs flex items-center justify-center flex-shrink-0 shadow">
                                            <?= htmlspecialchars(mb_strtoupper($initial, 'UTF-8')) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white text-sm"><?= htmlspecialchars($sName) ?></div>
                                            <div class="text-[10px] text-slate-500">ID: #<?= $st['student_id'] ?></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Username -->
                                <td class="py-3.5 px-4 font-mono text-slate-400 text-xs">
                                    @<?= htmlspecialchars($st['username']) ?>
                                </td>

                                <!-- Lớp -->
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-500/15 text-indigo-300 border border-indigo-500/25 font-bold text-[11px]">
                                        <i class="fa-solid fa-graduation-cap text-[9px]"></i>
                                        <?= htmlspecialchars($st['class_name'] ?? 'Chưa xếp lớp') ?>
                                    </span>
                                </td>

                                <!-- Ghi chú -->
                                <td class="py-3.5 px-4 text-slate-400 max-w-xs truncate">
                                    <?= !empty($st['notes']) ? htmlspecialchars($st['notes']) : '<span class="text-slate-600 italic">Chưa có ghi chú</span>' ?>
                                </td>

                                <!-- Thao tác -->
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= BASE_URL ?>/student/profile/<?= $st['student_id'] ?>"
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition flex items-center gap-1"
                                           title="Xem hồ sơ">
                                            <i class="fa-solid fa-id-badge text-amber-400 text-[11px]"></i>
                                            <span>Hồ sơ</span>
                                        </a>

                                        <button type="button"
                                                onclick="confirmDeleteStudent(<?= $st['student_id'] ?>, '<?= htmlspecialchars(addslashes($sName)) ?>')"
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-semibold transition"
                                                title="Xóa học sinh khỏi lớp">
                                            <i class="fa-solid fa-user-minus text-[11px]"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

</div>

<script>
// Lọc học sinh trong bảng
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('studentSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            document.querySelectorAll('.student-row').forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const cls = row.getAttribute('data-class') || '';
                if (!query || name.includes(query) || cls.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

async function confirmDeleteStudent(id, name) {
    const confirmed = await window.customConfirm(
        'Xóa học sinh khỏi lớp',
        `Bạn có chắc chắn muốn xóa học sinh "${name}" khỏi danh sách lớp do bạn quản lý không?`,
        true
    );

    if (confirmed) {
        window.location.href = '<?= BASE_URL ?>/student/delete/' + id;
    }
}
</script>

<?php require_once ROOT_PATH . '/app/Views/includes/footer.php'; ?>