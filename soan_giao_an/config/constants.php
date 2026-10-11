<?php

// ======================================================
// APPLICATION
// ======================================================

if (!defined('BASE_URL')) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
               || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
               || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    $protocol = $isHttps ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (str_contains($scriptName, '/edumanage/')) {
        $base = '/edumanage/soan_giao_an/public';
        $rootApp = '/edumanage';
    } else {
        // Chạy tại Root domain (Render / Cloud: https://edumanage-ankc.onrender.com)
        $base = '/soan_giao_an/public';
        $rootApp = '';
    }
    define('BASE_URL', $protocol . $host . $base);
    define('EDUMANAGE_URL', $protocol . $host . $rootApp);
}

define(
    'ROOT_PATH',
    dirname(__DIR__)
);


// ======================================================
// DATABASE (ĐỒNG BỘ VỚI EDUMANAGE)
// ======================================================

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'edumanage');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');


// ======================================================
// GROQ AI
// ======================================================

// DÁN API KEY GROQ CỦA BẠN VÀO ĐÂY (hoặc cấu hình qua constants.local.php / biến môi trường GROQ_API_KEY)
$localConfig = file_exists(__DIR__ . '/constants.local.php') ? include(__DIR__ . '/constants.local.php') : [];
define(
    'GROQ_API_KEY',
    $localConfig['GROQ_API_KEY'] ?? (getenv('GROQ_API_KEY') ?: 'YOUR_GROQ_API_KEY_HERE')
);

// Groq OpenAI-compatible Chat Completions API
define(
    'GROQ_API_URL',
    'https://api.groq.com/openai/v1/chat/completions'
);

// Model AI Groq
define(
    'GROQ_PRIMARY_MODEL',
    'openai/gpt-oss-20b'
);


// ======================================================
// SESSION (DÙNG CHUNG VỚI EDUMANAGE)
// ======================================================

define(
    'SESSION_USER_KEY',
    'user_logged_in'
);