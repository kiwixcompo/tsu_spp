<?php

/**
 * Application Configuration
 * PRODUCTION VERSION for staff.tsuniversity.edu.ng
 */

return [
    'name' => 'TSU Staff Portal',
    
    // Environment detection
    'env' => $_SERVER['HTTP_HOST'] === 'staff.tsuniversity.edu.ng' ? 'production' : 'local',
    
    // Debug mode (disabled in production)
    'debug' => $_SERVER['HTTP_HOST'] !== 'staff.tsuniversity.edu.ng',
    
    // Application URL
    'url' => $_SERVER['HTTP_HOST'] === 'staff.tsuniversity.edu.ng' 
        ? 'https://staff.tsuniversity.edu.ng' 
        : 'http://localhost/tsu_spp/public',
    
    // Timezone
    'timezone' => 'Africa/Lagos',
    
    // Session configuration
    'session' => [
        'lifetime' => 120, // minutes
        'cookie_secure' => $_SERVER['HTTP_HOST'] === 'staff.tsuniversity.edu.ng', // HTTPS only in production
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ],
];
