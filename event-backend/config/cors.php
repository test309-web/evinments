<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register', 'logout'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'], // تأكد من تطابق هذا مع عنوان React الخاص بك
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // هذا الإعداد بالغ الأهمية
];