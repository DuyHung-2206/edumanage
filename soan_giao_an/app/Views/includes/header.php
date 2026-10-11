<?php
require_once ROOT_PATH . '/app/Controllers/AuthController.php';
$currentUser = AuthController::currentUser();
$teacherCode = $currentUser['teacher_code'] ?? '';
$displayName = $currentUser['full_name'] ?? $currentUser['username'] ?? 'Giáo viên';
$currentUrl = $_GET['url'] ?? 'quiz/index';
?>
<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Soạn Giáo Án & Đề Thi AI - EduManage') ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        blue: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- EduManage Style -->
    <link rel="stylesheet" href="<?= EDUMANAGE_URL ?>/assets/css/style.css">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .nav-item:hover {
            background-color: #eff6ff;
            color: #2563eb;
        }
        .nav-item.active {
            background-color: #eff6ff;
            color: #2563eb;
            font-weight: 700;
        }
        .teacher-code-badge {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            padding: 0.35rem 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .teacher-code-text {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 800;
            color: #1e40af;
            letter-spacing: 0.05em;
        }
        .btn-copy-code {
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: background 0.15s;
        }
        .btn-copy-code:hover {
            background: #1d4ed8;
        }
        /* Printable styling */
        @media print {
            .no-print { display: none !important; }
            .sidebar { display: none !important; }
            .top-header { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- ================= UNIFIED SIDEBAR (CHUẨN GIAO DIỆN EDUMANAGE) ================= -->
    <aside id="side" class="sidebar fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 z-40 flex flex-col justify-between no-print">
        <div>
            <!-- Brand Header -->
            <div class="p-6 border-b border-slate-100 flex gap-3.5 items-center">
                <a href="<?= EDUMANAGE_URL ?>/index.php" class="w-10 h-10 rounded-xl bg-blue-600 text-white grid place-items-center text-xl shadow-md shadow-blue-500/20 font-bold hover:scale-105 transition-all">
                    🎓
                </a>
                <div>
                    <a href="<?= EDUMANAGE_URL ?>/index.php" class="text-base text-slate-800 font-extrabold tracking-tight hover:text-blue-600 transition-colors">EduManage</a>
                    <p class="text-xs text-blue-600 font-medium">Quản lý & Soạn Giáo Án</p>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <nav class="p-4 space-y-1 overflow-y-auto max-h-[calc(100vh-190px)]">
                <!-- PHÂN HỆ 1: QUẢN LÝ HỌC SINH -->
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 pt-1 pb-1">Quản lý học sinh</div>
                
                <a href="<?= EDUMANAGE_URL ?>/index.php" class="nav-item">
                    <span>📊</span> <span>Tổng quan & Học phí</span>
                </a>
                <a href="<?= EDUMANAGE_URL ?>/index.php" class="nav-item">
                    <span>📝</span> <span>Nhật ký buổi học</span>
                </a>
                <a href="<?= EDUMANAGE_URL ?>/index.php" class="nav-item">
                    <span>📈</span> <span>Bảng điểm chi tiết</span>
                </a>

                <!-- PHÂN HỆ 2: SOẠN GIÁO ÁN & ĐỀ THI AI -->
                <div class="text-[10px] font-bold uppercase tracking-wider text-blue-600 px-3 pt-4 pb-1">Soạn giáo án AI</div>

                <a href="<?= BASE_URL ?>/quiz/index" class="nav-item <?= ($currentUrl === 'quiz/index' || $currentUrl === '' || $currentUrl === 'index') ? 'active' : '' ?>">
                    <span>📚</span> <span>Kho giáo án & Đề thi</span>
                </a>
                <a href="<?= BASE_URL ?>/quiz/create" class="nav-item <?= str_contains($currentUrl, 'quiz/create') ? 'active' : '' ?>">
                    <span>🤖</span> <span>Soạn giáo án mới (AI)</span>
                </a>
            </nav>
        </div>

        <!-- User Profile Footer in Sidebar -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/60">
            <div class="flex items-center justify-between">
                <div class="overflow-hidden pr-2">
                    <p class="font-bold text-sm text-slate-800 truncate"><?= htmlspecialchars($displayName) ?></p>
                    <p class="text-xs text-blue-600 font-semibold font-mono"><?= htmlspecialchars($teacherCode ?: 'Giáo viên') ?></p>
                </div>
                <a href="<?= EDUMANAGE_URL ?>/api/auth.php?action=logout" class="text-xs font-semibold text-red-500 hover:text-red-700 bg-red-50 px-2.5 py-1.5 rounded-lg transition-all" title="Đăng xuất">
                    Đăng xuất
                </a>
            </div>
        </div>
    </aside>

    <!-- Mobile Overlay -->
    <div id="overlay" onclick="toggleSide()" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-30 md:hidden no-print"></div>

    <!-- MAIN WRAPPER -->
    <div class="md:ml-64 min-h-screen flex flex-col bg-slate-50 main-content">
        <!-- Top Header (Chuẩn Blue & White EduManage) -->
        <header class="top-header h-20 bg-white border-b border-slate-200 sticky top-0 z-20 flex items-center justify-between px-4 md:px-8 no-print">
            <div class="flex items-center gap-3">
                <button class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl" onclick="toggleSide()" aria-label="Toggle menu">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div>
                    <h1 class="font-extrabold text-xl text-slate-800"><?= htmlspecialchars($pageTitle ?? 'Soạn Giáo Án & Đề Thi AI') ?></h1>
                    <p class="text-xs text-slate-400">Trợ lý soạn giáo án & đề kiểm tra tiểu học bám sát chương trình GDPT mới</p>
                </div>
            </div>

            <!-- Right Header Actions -->
            <div class="flex items-center gap-3">
                <a href="<?= EDUMANAGE_URL ?>/index.php" class="hidden sm:inline-flex items-center gap-1.5 border border-blue-200 hover:bg-blue-50 text-blue-600 text-xs font-bold px-3.5 py-2 rounded-xl transition shadow-2xs">
                    <span>←</span> Về Quản lý học tập
                </a>

                <?php if (!empty($teacherCode)): ?>
                <div class="teacher-code-badge py-1.5 px-3">
                    <div class="text-left">
                        <span class="block text-[9px] font-bold tracking-wider text-slate-400 uppercase">Mã giáo viên</span>
                        <span id="teacherCodeVal" class="teacher-code-text text-sm"><?= htmlspecialchars($teacherCode) ?></span>
                    </div>
                    <button type="button" onclick="copyTeacherCode()" class="btn-copy-code py-1 px-2 text-xs" title="Sao chép mã giáo viên">
                        <i class="fa-regular fa-copy text-xs"></i>
                        <span id="copyBtnText" class="hidden sm:inline">Sao chép</span>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Main Body Container -->
        <main class="p-4 md:p-8 flex-1">