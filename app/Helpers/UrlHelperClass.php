<?php

namespace App\Helpers;

/**
 * URL Helper Class for TSU Staff Portal
 */
class UrlHelper
{
    /**
     * Get the base URL for the application
     */
    public static function base(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        return $protocol . '://' . $host . '/tsu_spp/public';
    }

    /**
     * Generate a URL for the given path
     */
    public static function url(string $path = ''): string
    {
        $base = self::base();
        $path = ltrim($path, '/');
        
        if (empty($path)) {
            return $base . '/';
        }
        
        return $base . '/' . $path;
    }

    /**
     * Generate an asset URL
     */
    public static function asset(string $path): string
    {
        return self::url($path);
    }
}