<?php
$pageTitle = 'Soạn Giáo Án & Tạo Đề AI';
require_once ROOT_PATH . '/app/Views/includes/header.php';
require_once ROOT_PATH . '/app/Views/includes/navbar.php';
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- BREADCRUMB & HEADER -->
    <div class="space-y-3">
        <a href="<?= BASE_URL ?>/quiz/index"
           class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition group">
            <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            <span>Quay lại kho đề</span>
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold mb-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                    <span>Trợ lý Khảo thí Tiểu học 2018</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                    Tạo Đề Kiểm Tra & Soạn Giáo Án Bằng AI
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Hệ thống tự động biên soạn câu hỏi bám sát chuẩn kiến thức kĩ năng GDPT mới và tạo kèm đáp án chấm chi tiết.
                </p>
            </div>
        </div>
    </div>

    <!-- MAIN FORM -->
    <form id="quizForm" method="POST" action="<?= BASE_URL ?>/quiz/generate" class="space-y-6">

        <!-- CARD 1: KHỐI LỚP & MÔN HỌC -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 space-y-6 shadow-sm">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                <span>Thông tin Khối lớp & Môn học</span>
            </h2>

            <!-- KHỐI LỚP (RADIO CHIPS) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">
                    Chọn Khối Lớp <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-5 gap-2 sm:gap-3">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="grade-radio-card cursor-pointer">
                            <input type="radio" name="grade" value="<?= $i ?>" <?= $i === 3 ? 'checked' : '' ?> class="sr-only">
                            <div class="p-3 text-center rounded-2xl border transition-all duration-200 <?= $i === 3 ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-500/25' : 'bg-slate-50 border-slate-200 text-slate-700 hover:border-blue-400' ?>">
                                <div class="text-base sm:text-lg font-black leading-none mb-1">Lớp <?= $i ?></div>
                                <div class="text-[10px] font-medium opacity-80">Tiểu học</div>
                            </div>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- MÔN HỌC (QUICK SELECT PILLS + INPUT) -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label for="subject" class="block text-xs font-bold text-slate-700">
                        Môn học <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Bấm nút chọn nhanh hoặc tự nhập</span>
                </div>

                <!-- Quick Pills -->
                <div class="flex flex-wrap gap-2" id="subjectPills">
                    <button type="button" data-subject="Toán" class="subject-pill px-3 py-1.5 rounded-xl text-xs font-bold transition border bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100">
                        <i class="fa-solid fa-calculator mr-1"></i> Toán
                    </button>
                    <button type="button" data-subject="Tiếng Việt" class="subject-pill px-3 py-1.5 rounded-xl text-xs font-bold transition border bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100">
                        <i class="fa-solid fa-book mr-1"></i> Tiếng Việt
                    </button>
                    <button type="button" data-subject="Tiếng Anh" class="subject-pill px-3 py-1.5 rounded-xl text-xs font-bold transition border bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100">
                        <i class="fa-solid fa-language mr-1"></i> Tiếng Anh
                    </button>
                    <button type="button" data-subject="Khoa học" class="subject-pill px-3 py-1.5 rounded-xl text-xs font-bold transition border bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100">
                        <i class="fa-solid fa-flask mr-1"></i> Khoa học
                    </button>
                    <button type="button" data-subject="Tự nhiên và Xã hội" class="subject-pill px-3 py-1.5 rounded-xl text-xs font-bold transition border bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100">
                        <i class="fa-solid fa-leaf mr-1"></i> TN & Xã hội
                    </button>
                    <button type="button" data-subject="Lịch sử và Địa lí" class="subject-pill px-3 py-1.5 rounded-xl text-xs font-bold transition border bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100">
                        <i class="fa-solid fa-earth-americas mr-1"></i> Lịch sử & Địa lí
                    </button>
                    <button type="button" data-subject="Tin học" class="subject-pill px-3 py-1.5 rounded-xl text-xs font-bold transition border bg-cyan-50 text-cyan-700 border-cyan-200 hover:bg-cyan-100">
                        <i class="fa-solid fa-desktop mr-1"></i> Tin học
                    </button>
                    <button type="button" data-subject="Đạo đức" class="subject-pill px-3 py-1.5 rounded-xl text-xs font-bold transition border bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100">
                        <i class="fa-solid fa-heart mr-1"></i> Đạo đức
                    </button>
                </div>

                <div class="relative">
                    <input type="text" id="subject" name="subject" required value="Toán"
                           placeholder="Ví dụ: Toán, Tiếng Việt, Khoa học..."
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                </div>
            </div>

            <!-- CHỦ ĐỀ / BÀI HỌC & GỢI Ý SGK -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label for="topic" class="block text-xs font-bold text-slate-700">
                        Bài học / Chủ đề kiểm tra <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-blue-600 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-lightbulb"></i> Gợi ý theo SGK
                    </span>
                </div>

                <input type="text" id="topic" name="topic" required
                       value="Phép nhân trong phạm vi 1000"
                       placeholder="Nhập tên bài học hoặc chủ đề kiểm tra..."
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">

                <!-- Dynamic Suggestions Container -->
                <div class="space-y-1.5">
                    <div class="text-[11px] font-semibold text-slate-400">Gợi ý bài học thường gặp (Bấm để chọn):</div>
                    <div id="suggestedTopics" class="flex flex-wrap gap-1.5">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: CẤU HÌNH ĐỀ THI -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 space-y-6 shadow-sm">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                <span>Cấu hình Định dạng Đề thi</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <!-- THỜI LƯỢNG -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Thời lượng kiểm tra</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="duration" value="15p" checked class="sr-only duration-radio">
                            <div class="duration-card p-3 rounded-2xl border text-center transition bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-500/20">
                                <div class="text-sm font-bold">15 Phút</div>
                                <div class="text-[10px] opacity-80">Kiểm tra nhanh</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="duration" value="45p" class="sr-only duration-radio">
                            <div class="duration-card p-3 rounded-2xl border text-center transition bg-slate-50 border-slate-200 text-slate-600 hover:border-blue-400">
                                <div class="text-sm font-bold">45 Phút</div>
                                <div class="text-[10px] opacity-80">1 tiết / Định kì</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- SỐ CÂU HỎI -->
                <div class="space-y-2">
                    <label for="num_questions" class="block text-xs font-bold text-slate-700">Số lượng câu hỏi</label>
                    <select id="num_questions" name="num_questions" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 transition">
                        <option value="5">5 câu (Đề ngắn)</option>
                        <option value="10" selected>10 câu (Chuẩn 15-45p)</option>
                        <option value="15">15 câu (Đề tổng hợp)</option>
                        <option value="20">20 câu (Ôn tập học kì)</option>
                    </select>
                </div>

                <!-- ĐỘ KHÓ -->
                <div class="space-y-2">
                    <label for="difficulty" class="block text-xs font-bold text-slate-700">Mức độ nhận thức</label>
                    <select id="difficulty" name="difficulty" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 transition">
                        <option value="Dễ">Dễ (Mức 1 - Nhận biết)</option>
                        <option value="Trung bình" selected>Trung bình (Mức 2 - Kết nối)</option>
                        <option value="Khá">Khá (Mức 3 - Vận dụng)</option>
                        <option value="Khó">Khó (Mức 4 - Vận dụng cao)</option>
                    </select>
                </div>
            </div>

            <!-- HÌNH THỨC CÂU HỎI (INTERACTIVE CARDS) -->
            <div class="space-y-2 pt-2">
                <label class="block text-xs font-bold text-slate-700">Hình thức đề thi</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    
                    <!-- Kết hợp -->
                    <label class="format-card-label cursor-pointer">
                        <input type="radio" name="format" value="mixed" checked class="sr-only format-radio">
                        <div class="format-card p-4 rounded-2xl border transition bg-blue-50 border-blue-300 text-blue-900 shadow-sm flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 text-sm">
                                <i class="fa-solid fa-cubes"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold">Kết hợp Trắc nghiệm & Tự luận</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Cân đối kiến thức toàn diện</div>
                            </div>
                        </div>
                    </label>

                    <!-- Trắc nghiệm -->
                    <label class="format-card-label cursor-pointer">
                        <input type="radio" name="format" value="multiple_choice" class="sr-only format-radio">
                        <div class="format-card p-4 rounded-2xl border transition bg-slate-50 border-slate-200 text-slate-700 hover:border-blue-400 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0 text-sm">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold">100% Trắc nghiệm</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">4 lựa chọn A, B, C, D</div>
                            </div>
                        </div>
                    </label>

                    <!-- Tự luận -->
                    <label class="format-card-label cursor-pointer">
                        <input type="radio" name="format" value="essay" class="sr-only format-radio">
                        <div class="format-card p-4 rounded-2xl border transition bg-slate-50 border-slate-200 text-slate-700 hover:border-blue-400 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0 text-sm">
                                <i class="fa-solid fa-pen-clip"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold">100% Tự luận</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Rèn luyện kĩ năng trình bày</div>
                            </div>
                        </div>
                    </label>

                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <div class="pt-2">
            <button type="submit" id="generateButton"
                    class="w-full py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-base transition-all shadow-lg shadow-blue-500/25 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-3">
                <i id="generateIcon" class="fa-solid fa-wand-magic-sparkles text-amber-300 text-lg"></i>
                <span id="generateText">Bắt Đầu Tạo Đề Bằng AI</span>
            </button>
        </div>

    </form>

</div>

<!-- MULTI-STEP LOADING MODAL (Pop-up on submit) -->
<div id="aiLoadingModal" class="fixed inset-0 z-[9999] bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-8 shadow-2xl space-y-6 text-center relative overflow-hidden">
        
        <!-- Header animation -->
        <div class="relative w-20 h-20 mx-auto">
            <div class="absolute inset-0 rounded-full border-4 border-blue-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center text-blue-600 text-2xl font-bold">
                <i class="fa-solid fa-brain"></i>
            </div>
        </div>

        <div class="space-y-1">
            <h3 class="text-lg font-bold text-slate-800">AI Đang Biên Soạn Đề Thi...</h3>
            <p class="text-xs text-slate-500">Quá trình thường mất khoảng 5 - 15 giây tùy độ dài đề thi.</p>
        </div>

        <!-- Multi-step checklist progress -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 space-y-3 text-left text-xs">
            <div id="step1" class="flex items-center gap-3 text-blue-600 font-bold transition-all">
                <i class="fa-solid fa-circle-notch animate-spin text-sm"></i>
                <span>Bước 1: Phân tích khung chương trình GDPT mới...</span>
            </div>
            <div id="step2" class="flex items-center gap-3 text-slate-400 font-medium transition-all">
                <i class="fa-regular fa-circle text-sm"></i>
                <span>Bước 2: Xây dựng ma trận & soạn thảo câu hỏi...</span>
            </div>
            <div id="step3" class="flex items-center gap-3 text-slate-400 font-medium transition-all">
                <i class="fa-regular fa-circle text-sm"></i>
                <span>Bước 3: Lập đáp án chuẩn và biểu điểm chi tiết...</span>
            </div>
        </div>

        <!-- Elapsed Timer -->
        <div class="text-[11px] font-semibold text-slate-400 flex items-center justify-center gap-1.5">
            <i class="fa-regular fa-clock"></i>
            <span>Thời gian đã qua: <strong id="elapsedTimer" class="text-blue-600 font-mono">0s</strong></span>
        </div>

    </div>
</div>

<script>
// Suggested Topics by Grade and Subject Database
const topicSuggestions = {
    '1': {
        'Toán': ['Các số từ 1 đến 10', 'Phép cộng trong phạm vi 10', 'Phép trừ trong phạm vi 10', 'Hình tròn, hình tam giác, hình vuông', 'Các số đến 100', 'Đo độ dài bằng xăng-ti-mét'],
        'Tiếng Việt': ['Âm vần cơ bản', 'Đọc hiểu đoạn văn ngắn', 'Viết câu đơn', 'Chính tả phân biệt c/k, g/gh'],
        'Tự nhiên và Xã hội': ['Gia đình của em', 'Trường học của em', 'Cơ thể người và chăm sóc sức khỏe', 'Cây cối và con vật quanh em']
    },
    '2': {
        'Toán': ['Phép cộng có nhớ trong phạm vi 100', 'Phép trừ có nhớ trong phạm vi 100', 'Bảng nhân 2 và nhân 5', 'Đo lường: Đề-xi-mét, Mét, Ki-lô-gam, Lít', 'Hình tứ giác, khối trụ, khối cầu'],
        'Tiếng Việt': ['Mở rộng vốn từ về gia đình', 'Đọc hiểu bài thơ', 'Tập làm văn kể về người thân', 'Viết đoạn văn ngắn về đồ vật'],
        'Tự nhiên và Xã hội': ['Nghề nghiệp trong xã hội', 'Thực vật và động vật', 'Thời tiết và các mùa trong năm']
    },
    '3': {
        'Toán': ['Bảng nhân 6, 7, 8, 9', 'Bảng chia 6, 7, 8, 9', 'Phép nhân trong phạm vi 1000', 'Phép chia có dư', 'Hình chữ nhật và hình vuông', 'Tính chu vi và diện tích'],
        'Tiếng Việt': ['Mở rộng vốn từ về trường học', 'So sánh và nhân hóa', 'Viết đoạn văn kể lại việc tốt', 'Luyện từ và câu: Dấu chấm, dấu phẩy'],
        'Tin học': ['Thông tin và xử lý thông tin', 'Sử dụng chuột và bàn phím máy tính', 'Tập gõ bàn phím đúng cách']
    },
    '4': {
        'Toán': ['Các số có nhiều chữ số', 'Phép nhân với số có hai chữ số', 'Dấu hiệu chia hết cho 2, 5, 9, 3', 'Phân số và các phép tính phân số', 'Hình bình hành và hình thoi'],
        'Tiếng Việt': ['Danh từ, Động từ, Tính từ', 'Luyện từ và câu: Trạng ngữ', 'Miêu tả con vật', 'Miêu tả cây cối'],
        'Khoa học': ['Nước và tính chất của nước', 'Không khí quanh ta', 'Ánh sáng và âm thanh', 'Sự trao đổi chất ở người'],
        'Lịch sử và Địa lí': ['Bản đồ và cách sử dụng bản đồ', 'Nước Văn Lang và Âu Lạc', 'Vùng Trung du và miền núi Bắc Bộ']
    },
    '5': {
        'Toán': ['Khái niệm số thập phân', 'Các phép tính với số thập phân', 'Tỉ số phần trăm và toán phần trăm', 'Hình thang, hình tròn, diện tích hình tròn', 'Vận tốc, quãng đường, thời gian (Toán chuyển động)'],
        'Tiếng Việt': ['Từ đồng nghĩa, từ trái nghĩa, từ nhiều nghĩa', 'Câu ghép và các cặp quan hệ từ', 'Miêu tả người', 'Tả cảnh thiên nhiên'],
        'Khoa học': ['Sự biến đổi của chất', 'Năng lượng mặt trời, gió, nước chảy', 'Môi trường và tài nguyên thiên nhiên'],
        'Lịch sử và Địa lí': ['Việt Nam đầu thế kỉ XX', 'Cách mạng tháng Tám 1945', 'Địa lí tự nhiên Việt Nam', 'Các châu lục trên thế giới']
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // 1. Grade Radio sync
    const gradeRadios = document.querySelectorAll('input[name="grade"]');
    gradeRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            gradeRadios.forEach(r => {
                const card = r.nextElementSibling;
                card.className = 'p-3 text-center rounded-2xl border transition-all duration-200 bg-slate-50 border-slate-200 text-slate-700 hover:border-blue-400';
            });
            const activeCard = radio.nextElementSibling;
            activeCard.className = 'p-3 text-center rounded-2xl border transition-all duration-200 bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-500/25';
            updateSuggestedTopics();
        });
    });

    // 2. Subject Quick Pills click
    const subjectInput = document.getElementById('subject');
    const pills = document.querySelectorAll('.subject-pill');
    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            const subj = pill.getAttribute('data-subject');
            if (subjectInput) {
                subjectInput.value = subj;
                updateSuggestedTopics();
            }
        });
    });

    if (subjectInput) {
        subjectInput.addEventListener('input', () => {
            updateSuggestedTopics();
        });
    }

    // 3. Dynamic Topic Suggestions
    function updateSuggestedTopics() {
        const selectedGrade = document.querySelector('input[name="grade"]:checked')?.value || '3';
        const currentSubj = (document.getElementById('subject')?.value || 'Toán').trim();
        const container = document.getElementById('suggestedTopics');
        if (!container) return;

        container.innerHTML = '';

        let matchedKey = Object.keys(topicSuggestions[selectedGrade] || {}).find(k => 
            currentSubj.toLowerCase().includes(k.toLowerCase()) || k.toLowerCase().includes(currentSubj.toLowerCase())
        );

        let topics = [];
        if (matchedKey && topicSuggestions[selectedGrade][matchedKey]) {
            topics = topicSuggestions[selectedGrade][matchedKey];
        } else {
            topics = topicSuggestions[selectedGrade]?.['Toán'] || ['Ôn tập giữa học kì', 'Ôn tập cuối học kì', 'Kiểm tra định kì chuyên đề'];
        }

        topics.forEach(t => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'px-3 py-1 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-700 border border-slate-200 text-[11px] font-semibold transition';
            btn.textContent = t;
            btn.addEventListener('click', () => {
                const topicInput = document.getElementById('topic');
                if (topicInput) topicInput.value = t;
            });
            container.appendChild(btn);
        });
    }

    // 4. Duration Radio visual sync
    document.querySelectorAll('.duration-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.duration-card').forEach(c => {
                c.className = 'duration-card p-3 rounded-2xl border text-center transition bg-slate-50 border-slate-200 text-slate-600 hover:border-blue-400';
            });
            const activeCard = radio.nextElementSibling;
            activeCard.className = 'duration-card p-3 rounded-2xl border text-center transition bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-500/20';
        });
    });

    // 5. Format Radio visual sync
    document.querySelectorAll('.format-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.format-card').forEach(c => {
                c.className = 'format-card p-4 rounded-2xl border transition bg-slate-50 border-slate-200 text-slate-700 hover:border-blue-400 flex items-start gap-3';
                const icon = c.querySelector('div:first-child');
                icon.className = 'w-9 h-9 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0 text-sm';
            });
            const activeCard = radio.nextElementSibling;
            activeCard.className = 'format-card p-4 rounded-2xl border transition bg-blue-50 border-blue-300 text-blue-900 shadow-sm flex items-start gap-3';
            activeCard.querySelector('div:first-child').className = 'w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 text-sm';
        });
    });

    // Initial suggestions load
    updateSuggestedTopics();

    // 6. Form Submission & Animated Loading Modal
    const form = document.getElementById('quizForm');
    const modal = document.getElementById('aiLoadingModal');
    const timerEl = document.getElementById('elapsedTimer');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const submitBtn = document.getElementById('generateButton');
    const submitText = document.getElementById('generateText');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
        submitText.textContent = 'Đang xử lí...';

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        let seconds = 0;
        const timerInterval = setInterval(() => {
            seconds++;
            timerEl.textContent = `${seconds}s`;

            if (seconds === 3) {
                step1.className = 'flex items-center gap-3 text-emerald-600 font-bold transition-all';
                step1.querySelector('i').className = 'fa-solid fa-check text-sm text-emerald-600';
                step2.className = 'flex items-center gap-3 text-blue-600 font-bold transition-all';
                step2.querySelector('i').className = 'fa-solid fa-circle-notch animate-spin text-sm text-blue-600';
            }
            if (seconds === 7) {
                step2.className = 'flex items-center gap-3 text-emerald-600 font-bold transition-all';
                step2.querySelector('i').className = 'fa-solid fa-check text-sm text-emerald-600';
                step3.className = 'flex items-center gap-3 text-blue-600 font-bold transition-all';
                step3.querySelector('i').className = 'fa-solid fa-circle-notch animate-spin text-sm text-blue-600';
            }
        }, 1000);

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            let rawText = await response.text();
            rawText = rawText.replace(/^\uFEFF/, '').trim();
            if (rawText.toLowerCase().startsWith('php')) {
                rawText = rawText.substring(3).trim();
            }
            const firstBrace = rawText.indexOf('{');
            if (firstBrace > 0) {
                rawText = rawText.substring(firstBrace);
            }

            let data;
            try {
                data = JSON.parse(rawText);
            } catch (err) {
                throw new Error('Dữ liệu trả về không hợp lệ: ' + rawText.substring(0, 300));
            }

            clearInterval(timerInterval);

            if (data.success) {
                step3.className = 'flex items-center gap-3 text-emerald-600 font-bold transition-all';
                step3.querySelector('i').className = 'fa-solid fa-check text-sm text-emerald-600';
                
                setTimeout(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.quiz_id) {
                        window.location.href = '<?= BASE_URL ?>/quiz/detail/' + data.quiz_id;
                    } else if (data.id) {
                        window.location.href = '<?= BASE_URL ?>/quiz/detail/' + data.id;
                    } else {
                        window.location.href = '<?= BASE_URL ?>/quiz/index';
                    }
                }, 500);
            } else {
                throw new Error(data.message || (data.detail ? data.detail : 'Không thể tạo đề kiểm tra.'));
            }

        } catch (error) {
            clearInterval(timerInterval);
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            submitText.textContent = 'Bắt Đầu Tạo Đề Bằng AI';

            alert('Lỗi tạo đề: ' + error.message);
        }
    });
});
</script>

<?php require_once ROOT_PATH . '/app/Views/includes/footer.php'; ?>
