<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - PlanSwift AI</title>
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

    <!-- Decorative background glow -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full glass-box rounded-3xl p-8 sm:p-10 space-y-7 shadow-2xl relative z-10">
        
        <!-- BRAND HEADER -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white text-2xl shadow-lg shadow-indigo-600/30 mb-1">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">PlanSwift <span class="text-indigo-400">AI</span></h1>
                <p class="text-xs text-slate-400 mt-1">Hệ thống Soạn Đề & Khảo Thí Tiểu Học Thông Minh</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-3.5 bg-rose-500/15 border border-rose-500/30 rounded-2xl text-rose-300 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-base flex-shrink-0 text-rose-400"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form action="<?= BASE_URL ?>/auth/login" method="POST" class="space-y-4">
            
            <!-- Tên đăng nhập -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-300">Tên đăng nhập</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="username" required placeholder="Nhập tên tài khoản..."
                           class="w-full pl-10 pr-4 py-3 bg-slate-900/90 border border-slate-700/80 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- Mật khẩu + Reveal Toggle -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-300">Mật khẩu</label>
                </div>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" id="loginPassword" name="password" required placeholder="••••••••"
                           class="w-full pl-10 pr-10 py-3 bg-slate-900/90 border border-slate-700/80 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                    <button type="button" onclick="togglePasswordVisibility('loginPassword', 'eyeIcon')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs focus:outline-none">
                        <i id="eyeIcon" class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Nút đăng nhập -->
            <button type="submit"
                    class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-extrabold text-sm rounded-2xl transition shadow-xl shadow-indigo-600/30 flex items-center justify-center space-x-2 mt-2">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                <span>Đăng Nhập</span>
            </button>
        </form>

        <div class="pt-2 border-t border-slate-800 text-center text-xs text-slate-400">
            Chưa có tài khoản? 
            <a href="<?= BASE_URL ?>/auth/register" class="text-indigo-400 font-bold hover:text-indigo-300 hover:underline">
                Đăng ký tài khoản mới
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
    </script>

</body>
</html>