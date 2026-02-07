<?php

/**
 * URL Helper Functions for TSU Staff Portal
 */

// Global helper functions (no namespace)

/**
 * Get the base URL for the application
 */
function getBaseUrl(): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Production server - detect actual path
    if ($host === 'staff.tsuniversity.edu.ng') {
        // Get the script name and remove index.php and any trailing parts
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = str_replace('/index.php', '', dirname($scriptName));
        
        // Remove only tsu_spp from path (keep public if present)
        $basePath = str_replace('/tsu_spp', '', $basePath);
        $basePath = rtrim($basePath, '/');
        
        // If base path is just '/', return domain only
        if ($basePath === '' || $basePath === '/') {
            return 'https://' . $host;
        }
        
        return 'https://' . $host . $basePath;
    }
    
    // Local development
    return $protocol . '://' . $host . '/tsu_spp/public';
}

/**
 * Generate a URL for the given path
 */
function url(string $path = ''): string
{
    $base = getBaseUrl();
    $path = ltrim($path, '/');
    
    if (empty($path)) {
        return $base . '/';
    }
    
    return $base . '/' . $path;
}

/**
 * Generate an asset URL
 */
function asset(string $path): string
{
    return url($path);
}