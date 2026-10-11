<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - PlanSwift AI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .glass-box {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Background glow -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full glass-box rounded-3xl p-8 sm:p-10 space-y-6 shadow-2xl relative z-10 my-8">
        
        <!-- HEADER -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white text-2xl shadow-lg shadow-indigo-600/30 mb-1">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Tạo Tài Khoản Mới</h1>
                <p class="text-xs text-slate-400 mt-0.5">Dành cho Giáo viên và Học sinh tiểu học</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-3.5 bg-rose-500/15 border border-rose-500/30 rounded-2xl text-rose-300 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-base flex-shrink-0 text-rose-400"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="p-4 bg-emerald-500/15 border border-emerald-500/30 rounded-2xl text-emerald-300 text-xs font-semibold text-center space-y-3">
                <p><?= htmlspecialchars($success) ?></p>
                <div>
                    <a href="<?= BASE_URL ?>/auth/login" class="inline-block px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-500 transition shadow-lg shadow-emerald-600/20">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i> Đến trang Đăng nhập ngay
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/auth/register" method="POST" class="space-y-4" id="registerForm">
            
            <!-- VAI TRÒ (GIÁO VIÊN HOẶC HỌC SINH) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300">Chọn vai trò của bạn:</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="teacher" checked class="sr-only role-radio">
                        <div class="role-card p-3 rounded-2xl border transition text-center bg-indigo-600/20 border-indigo-500 text-white shadow-md">
                            <i class="fa-solid fa-chalkboard-user text-indigo-400 text-lg mb-1 block"></i>
                            <div class="text-xs font-bold">Giáo viên</div>
                            <div class="text-[10px] text-slate-400">Tạo đề & Quản lý lớp</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="student" class="sr-only role-radio">
                        <div class="role-card p-3 rounded-2xl border transition text-center bg-slate-900/90 border-slate-800 text-slate-400 hover:border-slate-700">
                            <i class="fa-solid fa-user-graduate text-slate-500 text-lg mb-1 block"></i>
                            <div class="text-xs font-bold">Học sinh</div>
                            <div class="text-[10px] text-slate-400">Xem đề & Làm bài</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Họ và tên -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-300">Họ và Tên</label>
                <input type="text" name="full_name" required placeholder="Ví dụ: Nguyễn Văn An"
                       class="w-full px-4 py-2.5 bg-slate-900/90 border border-slate-700/80 rounded-2xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <!-- Tên đăng nhập -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-300">Tên đăng nhập (Username)</label>
                <input type="text" name="username" required placeholder="Viết liền không dấu, ví dụ: an_nguyen"
                       class="w-full px-4 py-2.5 bg-slate-900/90 border border-slate-700/80 rounded-2xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <!-- Mật khẩu -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-300">Mật khẩu (ít nhất 6 ký tự)</label>
                <div class="relative">
                    <input type="password" id="regPassword" name="password" required placeholder="••••••••"
                           class="w-full pl-4 pr-10 py-2.5 bg-slate-900/90 border border-slate-700/80 rounded-2xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                    <button type="button" onclick="togglePasswordVisibility('regPassword', 'eyeIcon1')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs focus:outline-none">
                        <i id="eyeIcon1" class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-300">Xác nhận mật khẩu</label>
                <div class="relative">
                    <input type="password" id="confirmPassword" name="confirm_password" required placeholder="••••••••"
                           class="w-full pl-4 pr-10 py-2.5 bg-slate-900/90 border border-slate-700/80 rounded-2xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                    <button type="button" onclick="togglePasswordVisibility('confirmPassword', 'eyeIcon2')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs focus:outline-none">
                        <i id="eyeIcon2" class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-extrabold text-sm rounded-2xl transition shadow-xl shadow-indigo-600/30 flex items-center justify-center space-x-2 mt-2">
                <i class="fa-solid fa-check"></i>
                <span>Hoàn Tất Đăng Ký</span>
            </button>
        </form>

        <div class="pt-2 border-t border-slate-800 text-center text-xs text-slate-400">
            Đã có tài khoản? 
            <a href="<?= BASE_URL ?>/auth/login" class="text-indigo-400 font-bold hover:text-indigo-300 hover:underline">
                Đăng nhập ngay
            </a>
        </div>

    </div>

    <script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-regular fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-regular fa-eye';
        }
    }

    // Role radio styling sync
    document.querySelectorAll('.role-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.role-card').forEach(c => {
                c.className = 'role-card p-3 rounded-2xl border transition text-center bg-slate-900/90 border-slate-800 text-slate-400 hover:border-slate-700';
                c.querySelector('i').className = c.querySelector('i').className.replace('text-indigo-400', 'text-slate-500');
            });
            const activeCard = radio.nextElementSibling;
            activeCard.className = 'role-card p-3 rounded-2xl border transition text-center bg-indigo-600/20 border-indigo-500 text-white shadow-md';
            activeCard.querySelector('i').className = activeCard.querySelector('i').className.replace('text-slate-500', 'text-indigo-400');
        });
    });
    </script>

</body>
</html>