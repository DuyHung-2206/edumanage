<?php 
require_once __DIR__.'/config/config.php'; 
if(!empty($_SESSION['user_id'])){
    header('Location:index.php');
    exit;
} 
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>EduManage - Đăng nhập & Đăng ký</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white rounded-3xl border border-blue-100 shadow-xl shadow-blue-500/5 p-8 relative overflow-hidden">
    <!-- Blue Accent Bar -->
    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-600 to-indigo-500"></div>

    <!-- App Brand -->
    <div class="flex items-center gap-3.5 mb-7">
      <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl grid place-items-center text-2xl shadow-md shadow-blue-500/20 font-bold">
        🎓
      </div>
      <div>
        <h1 class="font-extrabold text-2xl text-slate-800 tracking-tight">EduManage</h1>
        <p class="text-xs font-medium text-slate-400">Hệ thống Quản lý Học tập & Học phí</p>
      </div>
    </div>

    <!-- Mode Tabs (Đăng nhập / Đăng ký) -->
    <div class="flex gap-2 p-1 bg-slate-100 rounded-2xl mb-6">
      <button id="loginTab" class="flex-1 py-2.5 rounded-xl font-semibold text-sm transition-all bg-white text-blue-600 shadow-sm" onclick="mode('login')">
        Đăng nhập
      </button>
      <button id="regTab" class="flex-1 py-2.5 rounded-xl font-semibold text-sm text-slate-600 hover:text-slate-900 transition-all" onclick="mode('register')">
        Đăng ký
      </button>
    </div>

    <!-- Role Selection (Khi đăng ký) -->
    <div id="roleBox" class="hidden mb-5">
      <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Loại tài khoản</label>
      <div class="grid grid-cols-3 gap-2">
        <button type="button" class="roleBtn active" data-role="teacher">
          <span class="block text-base mb-0.5">👨‍🏫</span>Giáo viên
        </button>
        <button type="button" class="roleBtn" data-role="parent">
          <span class="block text-base mb-0.5">👨‍👩‍👧</span>Phụ huynh
        </button>
        <button type="button" class="roleBtn" data-role="student">
          <span class="block text-base mb-0.5">🎒</span>Học sinh
        </button>
      </div>
      
      <!-- Thông báo tự sinh mã cho Giáo viên -->
      <div id="teacherNotice" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800 flex items-start gap-2">
        <span class="text-blue-600 font-bold text-sm">💡</span>
        <div>
          <b>Mã giáo viên (GV-XXXXXXXX)</b> sẽ được hệ thống <b>tự động tạo ngay khi đăng ký</b>. Bạn không cần thêm học sinh trước để có mã.
        </div>
      </div>
    </div>

    <!-- Form -->
    <form id="authForm" class="space-y-4">
      <div id="nameBox" class="hidden">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Họ và tên</label>
        <input name="name" type="text" placeholder="Nguyễn Văn A" class="input">
      </div>

      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Địa chỉ Email</label>
        <input name="email" type="email" required placeholder="email@vi-du.com" class="input">
      </div>

      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mật khẩu</label>
        <input name="password" type="password" minlength="6" required placeholder="Tối thiểu 6 ký tự" class="input">
      </div>

      <!-- Mã kết nối học sinh: CHỈ dành cho Phụ huynh / Học sinh -->
      <div id="codeBox" class="hidden pt-1">
        <div class="flex justify-between items-center mb-1.5">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Mã kết nối học sinh</label>
          <span class="text-[11px] text-blue-600 font-semibold">Do giáo viên cung cấp</span>
        </div>
        <input name="connection_code" class="input uppercase tracking-wider font-mono font-semibold" placeholder="VD: 8A9B2C1D">
        <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
          * Phụ huynh/Học sinh dùng mã kết nối này để liên kết chính xác với hồ sơ học sinh của giáo viên.
        </p>
      </div>

      <button id="submitBtn" type="submit" class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl py-3.5 font-bold transition-all shadow-md shadow-blue-500/20 active:scale-[0.99] flex items-center justify-center gap-2">
        <span>Tiếp tục</span>
      </button>
    </form>

    <div id="msgBox" class="hidden mt-4 p-3 rounded-xl text-sm font-medium text-center"></div>
  </div>

  <script>
    let action = 'login';
    let role = 'teacher';

    const authForm = document.getElementById('authForm');
    const nameBox = document.getElementById('nameBox');
    const roleBox = document.getElementById('roleBox');
    const codeBox = document.getElementById('codeBox');
    const teacherNotice = document.getElementById('teacherNotice');
    const loginTab = document.getElementById('loginTab');
    const regTab = document.getElementById('regTab');
    const msgBox = document.getElementById('msgBox');
    const submitBtn = document.getElementById('submitBtn');

    function mode(m) {
      action = m;
      msgBox.classList.add('hidden');
      const isReg = m === 'register';

      nameBox.classList.toggle('hidden', !isReg);
      roleBox.classList.toggle('hidden', !isReg);
      updateRoleUI();

      if (isReg) {
        regTab.className = 'flex-1 py-2.5 rounded-xl font-semibold text-sm transition-all bg-white text-blue-600 shadow-sm';
        loginTab.className = 'flex-1 py-2.5 rounded-xl font-semibold text-sm text-slate-600 hover:text-slate-900 transition-all';
        submitBtn.querySelector('span').innerText = 'Tạo tài khoản';
      } else {
        loginTab.className = 'flex-1 py-2.5 rounded-xl font-semibold text-sm transition-all bg-white text-blue-600 shadow-sm';
        regTab.className = 'flex-1 py-2.5 rounded-xl font-semibold text-sm text-slate-600 hover:text-slate-900 transition-all';
        submitBtn.querySelector('span').innerText = 'Đăng nhập';
      }
    }

    function updateRoleUI() {
      const isReg = action === 'register';
      if (!isReg) {
        codeBox.classList.add('hidden');
        return;
      }

      if (role === 'teacher') {
        teacherNotice.classList.remove('hidden');
        codeBox.classList.add('hidden');
      } else {
        teacherNotice.classList.add('hidden');
        codeBox.classList.remove('hidden');
      }
    }

    document.querySelectorAll('.roleBtn').forEach(b => {
      b.onclick = () => {
        role = b.dataset.role;
        document.querySelectorAll('.roleBtn').forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        updateRoleUI();
      };
    });

    authForm.onsubmit = async (e) => {
      e.preventDefault();
      msgBox.classList.add('hidden');
      submitBtn.disabled = true;
      submitBtn.classList.add('opacity-70');

      let fd = new FormData(authForm);
      fd.append('action', action);
      if (action === 'register') {
        fd.append('role', role);
      }

      try {
        let r = await fetch('api/auth.php', { method: 'POST', body: fd });
        let d = await r.json();

        msgBox.textContent = d.message || '';
        msgBox.classList.remove('hidden');

        if (d.success) {
          msgBox.className = 'mt-4 p-3 rounded-xl text-sm font-medium text-center bg-emerald-50 text-emerald-700 border border-emerald-200';
          msgBox.textContent = action === 'register' ? 'Đăng ký thành công! Đang chuyển hướng...' : 'Đăng nhập thành công!';
          setTimeout(() => {
            location.href = 'index.php';
          }, 600);
        } else {
          msgBox.className = 'mt-4 p-3 rounded-xl text-sm font-medium text-center bg-red-50 text-red-700 border border-red-200';
        }
      } catch (err) {
        msgBox.textContent = 'Không thể kết nối máy chủ.';
        msgBox.className = 'mt-4 p-3 rounded-xl text-sm font-medium text-center bg-red-50 text-red-700 border border-red-200';
        msgBox.classList.remove('hidden');
      } finally {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-70');
      }
    };

    mode('login');
  </script>
</body>
</html>
