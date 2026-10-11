<?php
$pageTitle = 'Chi Tiết Đề Kiểm Tra & Giáo Án';
require_once ROOT_PATH . '/app/Views/includes/header.php';
require_once ROOT_PATH . '/app/Views/includes/navbar.php';

$lessonTitle = $quiz['lesson_name'] ?? $quiz['topic'] ?? 'Đề kiểm tra';
$subjectName = $quiz['subject'] ?? 'Chưa xác định';
$gradeNumber = $quiz['grade'] ?? '1';
$durationText = $quiz['duration'] ?? '15p';
$teacherName = $quiz['teacher_name'] ?? 'Giáo viên';
?>

<div class="max-w-5xl mx-auto space-y-6 pb-12">

    <!-- ============================================= -->
    <!-- TOOLBAR (NO PRINT) -->
    <!-- ============================================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 no-print shadow-sm">
        
        <!-- Left: Back Button & Title -->
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/quiz/index"
               class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition border border-slate-200"
               title="Quay lại danh sách">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <span class="text-[11px] font-extrabold uppercase text-blue-600 tracking-wider">
                    Lớp <?= htmlspecialchars($gradeNumber) ?> • Môn <?= htmlspecialchars($subjectName) ?>
                </span>
                <h2 class="text-sm font-bold text-slate-800 line-clamp-1">
                    <?= htmlspecialchars($lessonTitle) ?>
                </h2>
            </div>
        </div>

        <!-- Right: Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">

            <!-- Font Size Adjuster -->
            <div class="flex items-center rounded-xl bg-slate-100 border border-slate-200 p-0.5 text-xs">
                <button type="button" onclick="adjustFontSize(-1)" class="px-2.5 py-1 text-slate-600 hover:text-slate-900 transition font-bold" title="Thu nhỏ chữ">
                    A-
                </button>
                <span class="text-slate-300">|</span>
                <button type="button" onclick="adjustFontSize(1)" class="px-2.5 py-1 text-slate-600 hover:text-slate-900 transition font-bold" title="Phóng to chữ">
                    A+
                </button>
            </div>

            <!-- Toggle Đáp Án -->
            <button type="button" id="toggleAnswerBtn" onclick="toggleAnswerKey()"
                    class="px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs rounded-xl transition border border-amber-200 flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-eye-slash text-[11px]"></i>
                <span id="toggleAnswerText">Ẩn Đáp Án</span>
            </button>

            <!-- Copy Text Button -->
            <button type="button" onclick="copyQuizContent()"
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition border border-slate-200 flex items-center gap-1.5 shadow-2xs">
                <i class="fa-regular fa-copy text-[11px]"></i>
                <span>Sao chép</span>
            </button>

            <!-- Xuất File Word (.doc) -->
            <button type="button" onclick="exportToWord()"
                    class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl transition border border-blue-200 flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-file-word text-[11px]"></i>
                <span>Tải file Word</span>
            </button>

            <?php if ($currentUser && $currentUser['role'] === 'teacher'): ?>
                <!-- Nút Sửa Đề -->
                <a href="<?= BASE_URL ?>/quiz/edit/<?= (int)$quiz['id'] ?>"
                   class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs rounded-xl transition border border-amber-200 flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    <span>Sửa đề</span>
                </a>
            <?php endif; ?>

            <!-- In / Xuất PDF -->
            <button type="button" onclick="window.print()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-md shadow-blue-500/20">
                <i class="fa-solid fa-print text-[11px]"></i>
                <span>In / PDF</span>
            </button>

        </div>

    </div>

    <!-- ============================================= -->
    <!-- EXAM PAPER CONTAINER (WHITE A4 SHEET) -->
    <!-- ============================================= -->
    <div id="printPaper" class="bg-white text-slate-900 rounded-3xl shadow-sm overflow-hidden print-container border border-slate-200">

        <!-- HEADER ĐỀ THI HỌC ĐƯỜNG CHUẨN VIỆT NAM -->
        <div class="p-8 sm:p-12 border-b border-slate-200">
            
            <!-- School Header Table -->
            <div class="grid grid-cols-2 gap-4 pb-6 border-b-2 border-slate-900 text-xs text-slate-800">
                <div class="space-y-1">
                    <div class="font-extrabold uppercase tracking-wide">TRƯỜNG TIỂU HỌC: .................................................</div>
                    <div>HỌ VÀ TÊN: ....................................................................</div>
                    <div>LỚP: ............................ SBD: ............................................</div>
                </div>
                <div class="text-right space-y-1">
                    <div class="font-extrabold uppercase tracking-wide">BÀI KIỂM TRA ĐỊNH KÌ</div>
                    <div>MÔN: <strong class="text-slate-950 uppercase"><?= htmlspecialchars($subjectName) ?> - LỚP <?= htmlspecialchars($gradeNumber) ?></strong></div>
                    <div>THỜI GIAN LÀM BÀI: <strong><?= htmlspecialchars($durationText) ?></strong></div>
                </div>
            </div>

            <!-- Score & Teacher Comment Grid -->
            <div class="grid grid-cols-3 gap-0 mt-4 border-2 border-slate-900 rounded-lg overflow-hidden text-xs">
                <div class="col-span-1 border-r-2 border-slate-900 p-3 text-center bg-slate-50">
                    <div class="font-bold text-[11px] uppercase tracking-wider text-slate-700">Điểm số</div>
                    <div class="h-16 flex items-center justify-center font-bold text-2xl text-slate-400 italic">
                        ...... / 10
                    </div>
                </div>
                <div class="col-span-2 p-3 bg-slate-50">
                    <div class="font-bold text-[11px] uppercase tracking-wider text-slate-700">Lời phê của Thầy / Cô giáo:</div>
                    <div class="h-16 pt-2 text-slate-400 italic">
                        ....................................................................................................................................
                    </div>
                </div>
            </div>

            <!-- Quiz Title Banner -->
            <div class="text-center pt-8">
                <h1 class="text-xl sm:text-2xl font-black text-slate-950 uppercase tracking-tight">
                    <?= htmlspecialchars($lessonTitle) ?>
                </h1>
                <p class="text-xs text-slate-500 mt-1 italic">
                    (Đề thi gồm các câu hỏi bám sát chuẩn kiến thức kĩ năng Tiểu học)
                </p>
            </div>

        </div>

        <!-- ========================================= -->
        <!-- NỘI DUNG ĐỀ THI DO AI TẠO -->
        <!-- ========================================= -->
        <div class="p-8 sm:p-12">
            <div id="quiz-content" class="quiz-content text-slate-900 leading-relaxed font-serif">
                <?= $quiz['content'] ?? '' ?>
            </div>
        </div>

        <!-- ========================================= -->
        <!-- FOOTER BAR (NO PRINT) -->
        <!-- ========================================= -->
        <div class="px-8 py-5 border-t border-slate-100 bg-slate-50/60 no-print flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="text-slate-500">
                Tạo ngày <?= date('d/m/Y H:i', strtotime($quiz['created_at'])) ?>
                <?php if (!empty($teacherName)): ?>
                    • Biên soạn bởi: <strong class="text-slate-700"><?= htmlspecialchars($teacherName) ?></strong>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="exportToWord()" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-file-word text-blue-600"></i>
                    <span>Tải file Word</span>
                </button>
                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center gap-1.5 shadow-md shadow-blue-500/20">
                    <i class="fa-solid fa-print"></i>
                    <span>In đề thi</span>
                </button>
            </div>
        </div>

    </div>

</div>

<!-- STYLES CHO ĐỀ THI VÀ TRANG IN -->
<style>
.quiz-content {
    font-size: 15px;
    line-height: 1.8;
}

.quiz-content h1,
.quiz-content h2,
.quiz-content h3,
.quiz-content h4 {
    color: #0f172a;
    font-weight: 800;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.quiz-content h1 { font-size: 20px; text-align: center; }
.quiz-content h2 { font-size: 18px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
.quiz-content h3 { font-size: 16px; color: #1e293b; }

.quiz-content p {
    margin-bottom: 10px;
}

.quiz-content ol,
.quiz-content ul {
    margin: 10px 0 16px 24px;
}

.quiz-content li {
    margin-bottom: 6px;
}

.quiz-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 16px 0;
}

.quiz-content th,
.quiz-content td {
    border: 1px solid #cbd5e1;
    padding: 8px 12px;
    text-align: left;
}

.quiz-content th {
    background: #f8fafc;
    font-weight: 700;
}

.quiz-content strong {
    font-weight: 700;
    color: #0f172a;
}

.quiz-content hr {
    margin: 20px 0;
    border-color: #e2e8f0;
}

/* Ẩn phần đáp án khi giáo viên bật toggle */
.answers-hidden .answer-section {
    display: none !important;
}

@media print {
    body {
        background: #ffffff !important;
    }
    .no-print {
        display: none !important;
    }
    .print-container {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>

<script>
// 1. Phóng to / Thu nhỏ cỡ chữ
let currentFontSize = 15;
function adjustFontSize(step) {
    currentFontSize += step;
    if (currentFontSize < 12) currentFontSize = 12;
    if (currentFontSize > 22) currentFontSize = 22;
    const content = document.getElementById('quiz-content');
    if (content) {
        content.style.fontSize = currentFontSize + 'px';
    }
}

// 2. Toggle Ẩn / Hiện đáp án
let answersVisible = true;
function toggleAnswerKey() {
    const container = document.getElementById('quiz-content');
    const btnText = document.getElementById('toggleAnswerText');
    const btn = document.getElementById('toggleAnswerBtn');
    if (!container) return;

    // Tự động bọc phần đáp án nếu chưa có class answer-section
    const answerHeaders = container.querySelectorAll('h2, h3, h4');
    answerHeaders.forEach(h => {
        const text = h.innerText.toLowerCase();
        if (text.includes('đáp án') || text.includes('hướng dẫn chấm') || text.includes('dap an')) {
            let nextEl = h.nextElementSibling;
            h.classList.add('answer-section');
            while (nextEl) {
                nextEl.classList.add('answer-section');
                nextEl = nextEl.nextElementSibling;
            }
        }
    });

    answersVisible = !answersVisible;
    if (answersVisible) {
        container.classList.remove('answers-hidden');
        btnText.innerText = 'Ẩn Đáp Án';
        btn.classList.replace('text-slate-400', 'text-amber-700');
    } else {
        container.classList.add('answers-hidden');
        btnText.innerText = 'Hiện Đáp Án';
        btn.classList.replace('text-amber-700', 'text-slate-400');
    }
}

// 3. Sao chép nội dung text đề thi
function copyQuizContent() {
    const content = document.getElementById('quiz-content');
    if (!content) return;

    navigator.clipboard.writeText(content.innerText).then(() => {
        alert('Đã sao chép toàn bộ nội dung đề kiểm tra vào bộ nhớ tạm!');
    }).catch(err => {
        alert('Không thể sao chép: ' + err);
    });
}

// 4. Xuất file Word (.doc)
function exportToWord() {
    const header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' "+
        "xmlns:w='urn:schemas-microsoft-com:office:word' "+
        "xmlns='http://www.w3.org/TR/REC-html40'>"+
        "<head><meta charset='utf-8'><title><?= htmlspecialchars(addslashes($lessonTitle)) ?></title>"+
        "<style>body{font-family:'Times New Roman',serif; font-size:13pt; line-height:1.5;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid black;padding:6px;}</style>"+
        "</head><body>";
    const footer = "</body></html>";
    const sourceHTML = header + document.getElementById('printPaper').innerHTML + footer;

    const source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
    const fileDownload = document.createElement("a");
    document.body.appendChild(fileDownload);
    fileDownload.href = source;
    fileDownload.download = '<?= preg_replace('/[^a-zA-Z0-9_\-]/', '_', $lessonTitle) ?>.doc';
    fileDownload.click();
    document.body.removeChild(fileDownload);
}
</script>

<?php require_once ROOT_PATH . '/app/Views/includes/footer.php'; ?>