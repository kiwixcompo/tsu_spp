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
    
    // Production server - staff.tsuniversity.ng
    if ($host === 'staff.tsuniversity.ng') {
        return 'https://staff.tsuniversity.ng/public';
    }
    
    // Local development
    return $protocol . '://' . $host . '/public';
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