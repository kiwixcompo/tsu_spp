<?php
/**
 * Application Configuration
 */

return [
    'name' => $_ENV['APP_NAME'] ?? 'TSU Staff Profile Portal',
    'env' => ($_SERVER['HTTP_HOST'] ?? '') === 'staff.tsuniversity.ng' ? 'production' : ($_ENV['APP_ENV'] ?? 'local'),
    'debug' => ($_SERVER['HTTP_HOST'] ?? '') !== 'staff.tsuniversity.ng' && filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'url' => ($_SERVER['HTTP_HOST'] ?? '') === 'staff.tsuniversity.ng' ? 'https://staff.tsuniversity.ng' : ($_ENV['APP_URL'] ?? 'http://localhost/tsu_spp/public'),
    
    'session' => [
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 120),
        'encrypt' => filter_var($_ENV['SESSION_ENCRYPT'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'secure_cookie' => filter_var($_ENV['SESSION_SECURE_COOKIE'] ?? true, FILTER_VALIDATE_BOOLEAN),
    ],
    
    'upload' => [
        'max_size' => (int)($_ENV['UPLOAD_MAX_SIZE'] ?? 2097152), // 2MB
        'allowed_extensions' => explode(',', $_ENV['ALLOWED_EXTENSIONS'] ?? 'jpg,jpeg,png,pdf,doc,docx'),
        'path' => __DIR__ . '/../storage/uploads/',
    ],
    
    'pagination' => [
        'per_page' => 20,
        'max_per_page' => 100,
    ],
    
    'cache' => [
        'search_ttl' => 300, // 5 minutes
        'profile_ttl' => 3600, // 1 hour
    ],
    
    'security' => [
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
        'csrf_token_lifetime' => 3600, // 1 hour
    ],
];