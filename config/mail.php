<?php
/**
 * Mail Configuration
 */

return [
    'default' => 'smtp',
    
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
            'port' => $_ENV['MAIL_PORT'] ?? 587,
            'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            'username' => $_ENV['MAIL_USERNAME'] ?? null,
            'password' => $_ENV['MAIL_PASSWORD'] ?? null,
            'timeout' => 60,
        ],
    ],
    
    'from' => [
        'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@tsuniversity.edu.ng',
        'name' => $_ENV['MAIL_FROM_NAME'] ?? 'TSU Staff Portal',
    ],
    
    'templates' => [
        'verification' => 'emails/verification',
        'password_reset' => 'emails/password_reset',
        'profile_update' => 'emails/profile_update',
    ],
];