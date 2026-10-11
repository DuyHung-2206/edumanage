<?php 
require_once __DIR__.'/config/config.php'; 
if(empty($_SESSION['user_id'])){
    header('Location:login.php');
    exit;
} 
$role = $_SESSION['role']; 
$readonly = ($role !== 'teacher');

// Đảm bảo giáo viên luôn có teacher_code trong session và database
$teacherCode = '';
if($role === 'teacher'){
    if(empty($_SESSION['teacher_code'])){
        $st = db()->prepare('SELECT teacher_code FROM users WHERE id=?');
        $st->execute([$_SESSION['user_id']]);
        $row = $st->fetch();
        if(!empty($row['teacher_code'])){
            $_SESSION['teacher_code'] = $row['teacher_code'];
        } else {
            $newCode = teacher_code();
            db()->prepare('UPDATE users SET teacher_code=? WHERE id=?')->execute([$newCode, $_SESSION['user_id']]);
            $_SESSION['teacher_code'] = $newCode;
        }
    }
    $teacherCode = $_SESSION['teacher_code'];
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>EduManage - Quản lý học tập</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

  <!-- SIDEBAR (GIỮ NGUYÊN CẤU TRÚC - GIAO DIỆN BLUE & WHITE) -->
  <aside id="side" class="sidebar fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 z-40 flex flex-col justify-between">
    <div>
      <!-- Brand Header -->
      <div class="p-6 border-b border-slate-100 flex gap-3.5 items-center">
        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white grid place-items-center text-xl shadow-md shadow-blue-500/20 font-bold">
          🎓
        </div>
        <div>
          <b class="text-base text-slate-800 font-extrabold tracking-tight">EduManage</b>
          <p class="text-xs text-blue-600 font-medium"><?=e(role_label($role))?></p>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <nav class="p-4 space-y-1.5">
        <button onclick="showTab('overview')" class="nav active">
          <span>📊</span> <span>Tổng quan & Học phí</span>
        </button>
        <button onclick="showTab('lessons')" class="nav">
          <span>📝</span> <span>Nhật ký buổi học</span>
        </button>
        <button onclick="showTab('grades')" class="nav">
          <span>📈</span> <span>Bảng điểm chi tiết</span>
        </button>
        <?php if(!$readonly): ?>
        <button onclick="openPayment()" class="nav">
          <span>💳</span> <span>Cài đặt thanh toán</span>
        </button>

        <div class="pt-3 pb-1 px-3 text-[10px] font-extrabold uppercase tracking-wider text-blue-600">
          Soạn giáo án & Đề thi AI
        </div>
        <a href="soan_giao_an/public/quiz/index" class="nav block">
          <span>📚</span> <span>Kho giáo án & Đề thi</span>
        </a>
        <a href="soan_giao_an/public/quiz/create" class="nav block text-blue-600 font-bold">
          <span>🤖</span> <span>Tạo giáo án / Đề AI</span>
        </a>
        <?php endif; ?>
      </nav>
    </div>

    <!-- User Profile Footer in Sidebar -->
    <div class="p-4 border-t border-slate-100 bg-slate-50/60">
      <div class="flex items-center justify-between">
        <div class="overflow-hidden pr-2">
          <p class="font-bold text-sm text-slate-800 truncate"><?=e($_SESSION['user_name'])?></p>
          <p class="text-xs text-slate-400"><?=e(role_label($role))?></p>
        </div>
        <a href="api/auth.php?action=logout" class="text-xs font-semibold text-red-500 hover:text-red-700 bg-red-50 px-2.5 py-1.5 rounded-lg transition-all" title="Đăng xuất">
          Đăng xuất
        </a>
      </div>
    </div>
  </aside>

  <!-- Mobile Overlay -->
  <div id="overlay" onclick="toggleSide()" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-30 md:hidden"></div>

  <!-- MAIN WRAPPER -->
  <main class="md:ml-64 min-h-screen flex flex-col">
    <!-- Top Header -->
    <header class="h-20 bg-white border-b border-slate-200 sticky top-0 z-20 flex items-center justify-between px-4 md:px-8">
      <div class="flex items-center gap-3">
        <button class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl" onclick="toggleSide()">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <div>
          <h1 class="font-extrabold text-xl text-slate-800"><?php echo $readonly ? 'Hồ sơ học tập' : 'Quản lý học tập'; ?></h1>
          <p class="text-xs text-slate-400"><?php echo $readonly ? 'Theo dõi tiến trình buổi học, điểm số và học phí' : 'Quản lý học sinh, nhật ký buổi học, bảng điểm và học phí'; ?></p>
        </div>
      </div>

      <!-- Month Selector -->
      <div class="flex items-center gap-2">
        <label for="month" class="hidden sm:inline text-xs font-bold uppercase tracking-wider text-slate-400">Tháng:</label>
        <input id="month" type="month" class="border border-slate-200 bg-white rounded-xl px-3.5 py-2 text-sm font-semibold text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer shadow-sm" onchange="loadData()">
      </div>
    </header>

    <!-- Content Area -->
    <section class="p-4 md:p-8 flex-1 space-y-6">

      <?php if(!$readonly): ?>
      <!-- ================= HỒ SƠ TÀI KHOẢN GIÁO VIÊN & ĐƯA CẢ 2 MÃ RA NGOÀI CÙNG NHAU ================= -->
      <div class="teacher-profile-card">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow-md shadow-blue-500/20 font-bold shrink-0">
            👨‍🏫
          </div>
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <h2 class="text-lg md:text-xl font-extrabold text-slate-900"><?=e($_SESSION['user_name'])?></h2>
              <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full border border-blue-200">
                Tài khoản Giáo viên
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Hồ sơ giảng dạy chính thức trên EduManage</p>
          </div>
        </div>

        <!-- CẢ 2 MÃ ĐƯỢC ĐƯA RA NGOÀI CÙNG NHAU: MÃ GIÁO VIÊN & MÃ LIÊN KẾT HỌC SINH -->
        <div class="flex flex-wrap items-center gap-3">
          <!-- 1. MÃ GIÁO VIÊN -->
          <div class="teacher-code-badge">
            <div class="text-left">
              <div class="flex items-center gap-1.5 mb-0.5">
                <span class="block text-[10px] font-bold tracking-wider text-slate-400 uppercase">Mã giáo viên</span>
                <span class="text-[9px] bg-slate-100 text-slate-500 font-bold px-1.5 py-0.5 rounded">Tài khoản</span>
              </div>
              <span id="teacherCodeVal" class="teacher-code-text"><?=e($teacherCode)?></span>
            </div>
            <button type="button" onclick="copyTeacherCode()" class="btn-copy-code" title="Sao chép mã giáo viên">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
              <span id="copyBtnText">Sao chép</span>
            </button>
          </div>

          <!-- 2. MÃ LIÊN KẾT HỌC SINH (ĐƯA RA NGOÀI CÙNG VỚI MÃ GIÁO VIÊN) -->
          <div class="teacher-code-badge bg-blue-50/80 border-blue-300" id="topStudentCodeContainer">
            <div class="text-left">
              <div class="flex items-center gap-1.5 mb-0.5">
                <span class="block text-[10px] font-bold tracking-wider text-blue-700 uppercase">Mã liên kết học sinh</span>
                <span id="topStudentTag" class="text-[9px] bg-blue-200 text-blue-800 font-bold px-1.5 py-0.5 rounded truncate max-w-[110px]">Đang chọn</span>
              </div>
              <span id="topStudentCodeVal" class="teacher-code-text text-blue-900 font-mono">—</span>
            </div>
            <button type="button" onclick="copyTopStudentCode()" id="btnCopyTopStudent" class="btn-copy-code bg-indigo-600 hover:bg-indigo-700" title="Sao chép mã liên kết học sinh">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
              <span id="copyTopStudentText">Sao chép</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Quick AI Lesson & Quiz Tools Bar -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="soan_giao_an/public/quiz/create" class="flex items-center gap-4 p-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-sm hover:shadow-md hover:scale-[1.01] transition-all group">
          <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl group-hover:rotate-6 transition-transform">
            🤖
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="font-extrabold text-base">Soạn giáo án & Tạo đề AI</span>
              <span class="bg-amber-400 text-slate-900 font-extrabold text-[10px] px-2 py-0.5 rounded-full">Trí tuệ nhân tạo</span>
            </div>
            <p class="text-xs text-blue-100 mt-0.5">Sinh tự động đề trắc nghiệm, tự luận và kế hoạch bài dạy theo chuẩn GDPT</p>
          </div>
          <div class="ml-auto text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
          </div>
        </a>

        <a href="soan_giao_an/public/quiz/index" class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-200 text-slate-800 shadow-sm hover:shadow-md hover:border-blue-400 hover:scale-[1.01] transition-all group">
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            📚
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="font-extrabold text-base text-slate-900">Kho giáo án & Đề thi đã lưu</span>
              <span class="bg-blue-100 text-blue-700 font-bold text-[10px] px-2 py-0.5 rounded-full">Bộ sưu tập</span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Quản lý, xem trước khổ A4, xuất file Word (.doc) và in ấn đề thi</p>
          </div>
          <div class="ml-auto text-slate-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
          </div>
        </a>
      </div>
      <?php endif; ?>

      <!-- Layout 2 cột: Danh sách học sinh + Chi tiết học tập -->
      <div class="flex flex-col lg:flex-row gap-6">

        <!-- CỘT TRÁI: DANH SÁCH HỌC SINH -->
        <div class="lg:w-80 bg-white rounded-2xl border border-slate-200 p-4 h-fit shadow-sm">
          <div class="flex justify-between items-center mb-3 pb-3 border-b border-slate-100">
            <b class="text-sm font-bold text-slate-700"><?php echo $readonly ? 'Hồ sơ được liên kết' : 'Danh sách học sinh'; ?></b>
            <?php if(!$readonly): ?>
            <button onclick="openStudent()" class="w-7 h-7 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all font-bold flex items-center justify-center" title="Thêm học sinh mới">
              ＋
            </button>
            <?php endif; ?>
          </div>

          <div id="students" class="space-y-2 max-h-[600px] overflow-y-auto pr-1"></div>

          <?php if($readonly): ?>
          <button onclick="openLink()" class="w-full mt-4 border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-xl py-2.5 font-semibold text-sm transition-all flex items-center justify-center gap-1.5">
            <span>＋</span> Liên kết mã học sinh
          </button>
          <?php endif; ?>
        </div>

        <!-- CỘT PHẢI: WORKSPACE CHÍNH -->
        <div class="flex-1 min-w-0">

          <!-- TRẠNG THÁI RỖNG (CHƯA CÓ HỌC SINH) -->
          <div id="studentEmpty" class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 grid place-items-center text-3xl mx-auto mb-4">
              👨‍🎓
            </div>
            <h2 class="font-extrabold text-xl text-slate-800">Chưa có hồ sơ học sinh</h2>
            <p class="text-slate-400 text-sm mt-1.5 max-w-md mx-auto">
              <?php echo $readonly ? 'Tài khoản của bạn chưa được liên kết với học sinh nào. Hãy nhập mã kết nối do giáo viên cung cấp.' : 'Giáo viên đã có mã tài khoản độc lập. Hãy thêm hồ sơ học sinh đầu tiên để bắt đầu quản lý buổi học và học phí.'; ?>
            </p>
            <?php if(!$readonly): ?>
            <button onclick="openStudent()" class="mt-6 bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-3 font-bold transition-all shadow-md shadow-blue-500/20">
              ＋ Thêm học sinh đầu tiên
            </button>
            <?php else: ?>
            <button onclick="openLink()" class="mt-6 bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-3 font-bold transition-all shadow-md shadow-blue-500/20">
              Liên kết hồ sơ học sinh
            </button>
            <?php endif; ?>
          </div>

          <!-- DASHBOARD CHI TIẾT KHI ĐÃ CHỌN HỌC SINH -->
          <div id="dashboard" class="hidden space-y-6">

            <!-- Thẻ thông tin học sinh được chọn -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-wrap items-center justify-between gap-4 shadow-sm">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 grid place-items-center font-black text-lg border border-blue-200" id="avatar">
                  HS
                </div>
                <div>
                  <div class="flex items-center gap-2.5 flex-wrap">
                    <h2 id="sname" class="font-extrabold text-xl text-slate-900"></h2>
                    <span id="sclass" class="text-xs bg-slate-100 font-bold text-slate-600 px-2.5 py-0.5 rounded-md"></span>
                  </div>
                  <p id="teacherName" class="text-xs text-blue-600 font-semibold mt-1"></p>
                </div>
              </div>

              <!-- MÃ LIÊN KẾT HỌC SINH ĐƯA RA NGOÀI CÙNG TRÊN THẺ HỌC SINH -->
              <div class="flex items-center gap-3 flex-wrap">
                <div class="bg-blue-50 border border-blue-200 rounded-xl px-3.5 py-2 flex items-center gap-3">
                  <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-blue-700">Mã liên kết học sinh này</span>
                    <span id="studentConnectionCodeVal" class="font-mono text-base font-black text-blue-900 tracking-wider">—</span>
                  </div>
                  <button type="button" onclick="copyCurrentStudentCode()" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-2.5 py-1.5 text-xs font-bold transition-all flex items-center gap-1 shadow-sm" title="Sao chép mã liên kết của học sinh này">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span id="copyStudentBtnText">Sao chép</span>
                  </button>
                </div>

                <?php if(!$readonly): ?>
                <div class="flex items-center gap-2">
                  <button onclick="editStudent()" class="border border-slate-200 hover:border-blue-300 hover:bg-blue-50 text-slate-700 rounded-xl px-4 py-2 font-semibold text-sm transition-all">
                    Chỉnh sửa
                  </button>
                  <button onclick="deleteStudent()" class="text-red-600 border border-red-100 hover:bg-red-50 rounded-xl px-4 py-2 font-semibold text-sm transition-all">
                    Xóa
                  </button>
                </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Thanh điều hướng Tab -->
            <div class="flex bg-white border border-slate-200 rounded-2xl p-1.5 gap-2 overflow-x-auto shadow-sm">
              <button class="tab active" onclick="showTab('overview')">Phiếu học phí & Báo cáo</button>
              <button class="tab" onclick="showTab('lessons')">Nhật ký buổi học</button>
              <button class="tab" onclick="showTab('grades')">Bảng điểm chi tiết</button>
            </div>

            <!-- TAB 1: TỔNG QUAN & HỌC PHÍ -->
            <div id="overview" class="page space-y-6">

              <!-- NÚT XUẤT HÌNH ẢNH PHIẾU HỌC PHÍ & BÁO CÁO -->
              <div class="flex justify-end">
                <button
                  type="button"
                  onclick="exportOverviewImage()"
                  class="export-image-btn border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-xl px-4 py-2 font-semibold text-sm transition-all shadow-sm flex items-center gap-2"
                  title="Xuất phiếu học phí và báo cáo thành hình ảnh"
                >
                  🖼️ <span>Xuất hình ảnh</span>
                </button>
              </div>

              <!-- 4 Thẻ thống kê -->
              <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="stat">
                  <span>Số buổi học</span>
                  <b id="statLessons">0</b>
                </div>
                <div class="stat">
                  <span>Tổng số giờ</span>
                  <b id="statHours">0</b>
                </div>
                <div class="stat">
                  <span>Tổng học phí</span>
                  <b id="statPrice">0 đ</b>
                </div>
                <div class="stat">
                  <span>Điểm TB</span>
                  <b id="statAvg">—</b>
                </div>
              </div>

              <!-- Chi tiết học phí + Lịch học -->
              <div class="grid xl:grid-cols-3 gap-6">
                <!-- Cột Học phí & Thanh toán -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col justify-between">
                  <div>
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-600 text-white p-6">
                      <p class="text-xs font-bold uppercase tracking-wider text-blue-100 mb-1">TỔNG HỌC PHÍ THÁNG</p>
                      <b id="totalFee" class="text-3xl font-extrabold tracking-tight">0 đ</b>
                    </div>
                    <div class="p-6 space-y-5">
                      <div class="text-center">
                        <img id="qr" class="w-44 h-44 object-contain mx-auto bg-slate-50 border border-slate-100 rounded-xl p-2 shadow-sm" src="" alt="Mã QR thanh toán">
                      </div>
                      <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between py-1.5 border-b border-slate-50">
                          <span class="text-slate-500">Ngân hàng</span>
                          <b id="bank" class="text-slate-800">Chưa thiết lập</b>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-50">
                          <span class="text-slate-500">Số tài khoản</span>
                          <b id="account" class="font-mono text-slate-800">—</b>
                        </div>
                        <div class="flex justify-between py-1.5">
                          <span class="text-slate-500">Chủ tài khoản</span>
                          <b id="owner" class="text-slate-800">—</b>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php if(!$readonly): ?>
                  <div class="p-6 pt-0">
                    <button onclick="openPayment()" class="w-full border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-xl py-3 font-semibold text-sm transition-all">
                      Cài đặt thông tin thanh toán
                    </button>
                  </div>
                  <?php endif; ?>
                </div>

                <!-- Cột Lịch học trong tháng -->
                <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                  <div class="flex justify-between items-center mb-5">
                    <div>
                      <h3 class="font-bold text-lg text-slate-800">Lịch học trong tháng</h3>
                      <p class="text-xs text-slate-400">Các ngày học sinh đã tham gia buổi học</p>
                    </div>
                    <?php if(!$readonly): ?>
                    <button onclick="openLesson()" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-2 font-semibold text-sm transition-all shadow-sm">
                      ＋ Buổi học
                    </button>
                    <?php endif; ?>
                  </div>
                  <div id="calendar" class="grid grid-cols-7 gap-2"></div>
                </div>
              </div>
            </div>

            <!-- TAB 2: NHẬT KÝ BUỔI HỌC -->
            <div id="lessons" class="page hidden">
              <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                  <div>
                    <h3 class="font-bold text-lg text-slate-800">Nhật ký buổi học</h3>
                    <p class="text-xs text-slate-400">Chi tiết các buổi học trong tháng được chọn</p>
                  </div>
                  <div class="flex items-center gap-2">
                    <button
                      type="button"
                      onclick="exportLessonsImage()"
                      class="export-image-btn border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-xl px-4 py-2 font-semibold text-sm transition-all shadow-sm flex items-center gap-2"
                      title="Xuất nhật ký buổi học thành hình ảnh"
                    >
                      🖼️ <span>Xuất hình ảnh</span>
                    </button>

                    <?php if(!$readonly): ?>
                    <button onclick="openLesson()" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-2 font-semibold text-sm transition-all shadow-sm">
                      ＋ Buổi học
                    </button>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="overflow-x-auto">
                  <table class="w-full min-w-[1000px] text-sm">
                    <thead>
                      <tr>
                        <th>STT</th>
                        <th>Ngày học</th>
                        <th>Khung giờ</th>
                        <th>Học phí</th>
                        <th>Nội dung môn học</th>
                        <th>BTVN</th>
                        <th>Ý thức</th>
                        <th>Nhận xét</th>
                        <?php if(!$readonly): ?><th>Thao tác</th><?php endif; ?>
                      </tr>
                    </thead>
                    <tbody id="lessonRows"></tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- TAB 3: BẢNG ĐIỂM CHI TIẾT -->
            <div id="grades" class="page hidden space-y-6">
              <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 md:col-span-2 shadow-sm">
                  <span class="text-xs font-bold uppercase tracking-wider text-slate-400">ĐIỂM TRUNG BÌNH THÁNG</span>
                  <b id="bigAvg" class="block text-4xl text-blue-600 font-extrabold mt-2">—</b>
                </div>
                <div class="bg-blue-600 text-white rounded-2xl p-6 shadow-md shadow-blue-500/20">
                  <span class="text-xs font-bold uppercase tracking-wider text-blue-100">Bài kiểm tra</span>
                  <b id="gradeCount" class="block text-4xl font-extrabold mt-2">0</b>
                </div>
              </div>

              <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                  <div class="flex items-center gap-2">
                    <button
                      type="button"
                      onclick="exportGradesImage()"
                      class="export-image-btn border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-xl px-4 py-2 font-semibold text-sm transition-all shadow-sm flex items-center gap-2"
                      title="Xuất bảng điểm chi tiết thành hình ảnh"
                    >
                      🖼️ <span>Xuất hình ảnh</span>
                    </button>

                    <?php if(!$readonly): ?>
                    <button onclick="openGrade()" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-2 font-semibold text-sm transition-all shadow-sm">
                      ＋ Bài kiểm tra
                    </button>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="overflow-x-auto">
                  <table class="w-full min-w-[800px] text-sm">
                    <thead>
                      <tr>
                        <th>Ngày</th>
                        <th>Tên bài kiểm tra</th>
                        <th>Điểm số</th>
                        <th>Lỗi sai cần khắc phục</th>
                        <?php if(!$readonly): ?><th>Thao tác</th><?php endif; ?>
                      </tr>
                    </thead>
                    <tbody id="gradeRows"></tbody>
                  </table>
                </div>
              </div>

              <!-- Nhận xét học tập & Lộ trình -->
              <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                  <h3 class="font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Nhận xét học tập tháng</h3>
                  <div id="feedbackText" class="text-sm leading-relaxed text-slate-600">Chưa có nhận xét.</div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                  <h3 class="font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Mục tiêu & Lộ trình tháng tới</h3>
                  <div id="roadmapText" class="text-sm leading-relaxed text-slate-600">Chưa có mục tiêu.</div>
                  <?php if(!$readonly): ?>
                  <button onclick="openFeedback()" class="mt-5 border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-xl px-4 py-2 text-sm font-semibold transition-all">
                    Cập nhật nhận xét & lộ trình
                  </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- MODAL CONTAINER CHUNG -->
  <div id="modal" class="modal hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div id="modalBox" class="bg-white rounded-3xl w-full max-w-xl max-h-[92vh] overflow-y-auto border border-slate-100"></div>
  </div>

  <!-- TOAST NOTIFICATION CHO SAO CHÉP MÃ -->
  <div id="copyToast" class="edu-toast">
    <span class="text-emerald-400 font-bold">✓</span>
    <span id="toastMsg">Đã sao chép mã vào bộ nhớ tạm!</span>
  </div>

  <script>
    window.USER_ROLE = <?=json_encode($role)?>;
    window.TEACHER_CODE = <?=json_encode($teacherCode)?>;

    // Hàm sao chép mã giáo viên
    function copyTeacherCode() {
      const el = document.getElementById('teacherCodeVal');
      if (!el) return;
      const code = el.innerText.trim();
      if (!code || code === 'Chưa cập nhật') return;

      navigator.clipboard.writeText(code).then(() => {
        const btnText = document.getElementById('copyBtnText');
        const oldText = btnText.innerText;
        btnText.innerText = 'Đã chép!';

        const toast = document.getElementById('copyToast');
        document.getElementById('toastMsg').innerText = `Đã sao chép mã giáo viên (${code})!`;
        toast.classList.add('show');

        setTimeout(() => {
          btnText.innerText = oldText;
          toast.classList.remove('show');
        }, 2200);
      }).catch(err => {
        alert('Không thể sao chép: ' + err);
      });
    }

    // Hàm sao chép mã kết nối học sinh ở header trên cùng
    function copyTopStudentCode() {
      const el = document.getElementById('topStudentCodeVal');
      if (!el) return;
      const code = el.innerText.trim();
      if (!code || code === '—' || code === 'Chưa có') {
        alert('Vui lòng chọn một học sinh có mã liên kết để sao chép.');
        return;
      }

      navigator.clipboard.writeText(code).then(() => {
        const btnText = document.getElementById('copyTopStudentText');
        const oldText = btnText.innerText;
        btnText.innerText = 'Đã chép!';

        const toast = document.getElementById('copyToast');
        document.getElementById('toastMsg').innerText = `Đã sao chép mã kết nối học sinh (${code})!`;
        toast.classList.add('show');

        setTimeout(() => {
          btnText.innerText = oldText;
          toast.classList.remove('show');
        }, 2200);
      }).catch(err => {
        alert('Không thể sao chép: ' + err);
      });
    }

    // Hàm sao chép mã kết nối học sinh tại thẻ học sinh đang chọn
    function copyCurrentStudentCode() {
      const el = document.getElementById('studentConnectionCodeVal');
      if (!el) return;
      const code = el.innerText.trim();
      if (!code || code === '—') return;

      navigator.clipboard.writeText(code).then(() => {
        const btnText = document.getElementById('copyStudentBtnText');
        const oldText = btnText.innerText;
        btnText.innerText = 'Đã chép!';

        const toast = document.getElementById('copyToast');
        document.getElementById('toastMsg').innerText = `Đã sao chép mã kết nối học sinh (${code})!`;
        toast.classList.add('show');

        setTimeout(() => {
          btnText.innerText = oldText;
          toast.classList.remove('show');
        }, 2200);
      }).catch(err => {
        alert('Không thể sao chép: ' + err);
      });
    }
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="assets/js/app.js"></script>
</body>
</html>
