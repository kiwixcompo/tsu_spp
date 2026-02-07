<?php

/**
 * URL Helper Functions for TSU Staff Portal
 * PRODUCTION VERSION for staff.tsuniversity.edu.ng
 */

// Global helper functions (no namespace)

/**
 * Get the base URL for the application
 */
function getBaseUrl(): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Production server
    if ($host === 'staff.tsuniversity.edu.ng') {
        return 'https://staff.tsuniversity.edu.ng';
    }
    
    // Local development fallback
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
