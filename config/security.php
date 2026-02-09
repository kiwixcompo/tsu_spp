<?php
/**
 * Security Configuration
 * Enhanced security measures to protect against malware, viruses, and attacks
 */

return [
    // File Upload Security
    'upload' => [
        // Allowed file extensions for profile photos
        'allowed_photo_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        
        // Allowed file extensions for documents (CV, etc.)
        'allowed_document_extensions' => ['pdf', 'doc', 'docx'],
        
        // Maximum file sizes (in bytes)
        'max_photo_size' => 5 * 1024 * 1024, // 5MB
        'max_document_size' => 10 * 1024 * 1024, // 10MB
        
        // Blocked extensions (potential malware vectors)
        'blocked_extensions' => [
            'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar',
            'php', 'php3', 'php4', 'php5', 'phtml', 'asp', 'aspx', 'jsp',
            'sh', 'bash', 'cgi', 'pl', 'py', 'rb', 'dll', 'so', 'dylib',
            'app', 'deb', 'rpm', 'dmg', 'pkg', 'msi', 'apk', 'ipa'
        ],
        
        // MIME type validation
        'allowed_photo_mimes' => [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif'
        ],
        
        'allowed_document_mimes' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
    ],
    
    // Input Sanitization
    'input' => [
        // Strip dangerous HTML tags
        'strip_tags' => true,
        
        // Allowed HTML tags (for rich text fields)
        'allowed_tags' => '<p><br><strong><em><u><a><ul><ol><li>',
        
        // Maximum input lengths
        'max_text_length' => 1000,
        'max_name_length' => 100,
        'max_email_length' => 255,
    ],
    
    // Session Security
    'session' => [
        'regenerate_interval' => 300, // 5 minutes
        'lifetime' => 3600, // 1 hour
        'secure' => true, // HTTPS only
        'httponly' => true, // No JavaScript access
        'samesite' => 'Strict', // CSRF protection
    ],
    
    // Rate Limiting
    'rate_limit' => [
        'login_attempts' => 5,
        'login_lockout_time' => 900, // 15 minutes
        'api_requests_per_minute' => 60,
        'form_submissions_per_hour' => 20,
    ],
    
    // CSRF Protection
    'csrf' => [
        'enabled' => true,
        'token_length' => 32,
        'token_lifetime' => 3600, // 1 hour
    ],
    
    // XSS Protection
    'xss' => [
        'enabled' => true,
        'filter_input' => true,
        'filter_output' => true,
    ],
    
    // SQL Injection Protection
    'sql' => [
        'use_prepared_statements' => true,
        'escape_output' => true,
    ],
    
    // Password Security
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special_chars' => true,
        'hash_algorithm' => PASSWORD_ARGON2ID,
    ],
    
    // Headers Security
    'headers' => [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data: https:; font-src 'self' data: https://cdnjs.cloudflare.com;",
    ],
    
    // IP Blocking
    'ip_blocking' => [
        'enabled' => true,
        'blocked_ips' => [],
        'whitelist_ips' => [],
    ],
    
    // Logging
    'logging' => [
        'log_failed_logins' => true,
        'log_suspicious_activity' => true,
        'log_file_uploads' => true,
        'log_admin_actions' => true,
    ],
];
