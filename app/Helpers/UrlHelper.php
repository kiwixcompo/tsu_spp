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
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']); // e.g., /tsu_spp/public
    $scriptPath = str_replace('\\', '/', $scriptPath); // Normalize slashes for Windows
    
    // If the path ends in /public and we are rewriting to it, we can either keep it or strip it.
    // To match what the user is experiencing, we'll keep the dynamically derived path which preserves the 'tsu_spp' folder.
    // However, if we want clean URLs (http://localhost/tsu_spp/admin), we can strip /public.
    // The previous hardcoded string was '/public', which wiped out the local folder. 
    // Just using the actual script directory ensures it adapts to whatever folder WAMP uses.
    
    return $protocol . '://' . $host . $scriptPath;
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