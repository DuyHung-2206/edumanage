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

