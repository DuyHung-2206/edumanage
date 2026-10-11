<?php

require_once __DIR__ . '/config/constants.php';

echo '<h2>Test Groq API</h2>';

$data = [
    'model' => GROQ_PRIMARY_MODEL,

    'messages' => [
        [
            'role' => 'user',
            'content' => 'Hãy trả lời: Xin chào, Groq API đang hoạt động.'
        ]
    ],

    'temperature' => 0.5,

    'max_completion_tokens' => 100
];

$ch = curl_init(GROQ_API_URL);

curl_setopt_array(
    $ch,
    [
        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS => json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        ),

        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . GROQ_API_KEY,
            'Content-Type: application/json'
        ],

        CURLOPT_TIMEOUT => 60
    ]
);

$response = curl_exec($ch);

$curlError = curl_error($ch);

$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);

curl_close($ch);

echo '<p><strong>HTTP Status:</strong> ' .
     htmlspecialchars((string)$httpCode) .
     '</p>';

if (!empty($curlError)) {

    echo '<p><strong>cURL Error:</strong> ' .
         htmlspecialchars($curlError) .
         '</p>';
}

echo '<h3>Groq Response:</h3>';

echo '<pre>';
echo htmlspecialchars($response);
echo '</pre>';