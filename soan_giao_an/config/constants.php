<?php

// ======================================================
// APPLICATION
// ======================================================

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (str_contains($scriptName, '/edumanage/soan_giao_an/public')) {
        $base = '/edumanage/soan_giao_an/public';
    } elseif (str_contains($scriptName, '/edumanage/soan_giao_an')) {
        $base = '/edumanage/soan_giao_an/public';
    } elseif (str_contains($scriptName, '/soan_giao_an/public')) {
        $base = '/soan_giao_an/public';
    } elseif (str_contains($scriptName, '/soan_giao_an')) {
        $base = '/soan_giao_an/public';
    } else {
        $base = '/edumanage/soan_giao_an/public';
    }
    define('BASE_URL', $protocol . $host . $base);
}

if (!defined('EDUMANAGE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('EDUMANAGE_URL', $protocol . $host . '/edumanage');
}

define(
    'ROOT_PATH',
    dirname(__DIR__)
);


// ======================================================
// DATABASE (ĐỒNG BỘ VỚI EDUMANAGE)
// ======================================================

define(
    'DB_HOST',
    'localhost'
);

define(
    'DB_NAME',
    'edumanage'
);

define(
    'DB_USER',
    'root'
);

define(
    'DB_PASS',
    ''
);

define(
    'DB_CHARSET',
    'utf8mb4'
);


// ======================================================
// GROQ AI
// ======================================================

// DÁN API KEY GROQ CỦA BẠN VÀO ĐÂY (hoặc cấu hình qua biến môi trường GROQ_API_KEY)
define(
    'GROQ_API_KEY',
    getenv('GROQ_API_KEY') ?: 'YOUR_GROQ_API_KEY_HERE'
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