<?php

namespace App\Helpers;

/**
 * QR Code Helper
 * Generates QR codes for user profiles using a reliable API
 */
class QRCodeHelper
{
    private static $qrCodeDir = __DIR__ . '/../../storage/qrcodes/';
    
    /**
     * Generate QR code for a profile
     */
    public static function generateProfileQRCode(int $userId, string $profileSlug): ?string
    {
        try {
            // Create directory if it doesn't exist
            if (!is_dir(self::$qrCodeDir)) {
                if (!mkdir(self::$qrCodeDir, 0755, true)) {
                    error_log("Failed to create QR code directory: " . self::$qrCodeDir);
                    return null;
                }
            }
            
            // Load URL helper if not available
            if (!function_exists('url')) {
                require_once __DIR__ . '/UrlHelper.php';
            }
            
            // Profile URL
            $profileUrl = url('profile/' . $profileSlug);
            
            // Generate QR code using QRServer API (Reliable alternative to Google)
            $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&data=' . urlencode($profileUrl);
            
            // Download QR code data
            $qrCodeData = self::downloadData($apiUrl);
            
            if (!$qrCodeData) {
                // Try backup API (QuickChart)
                $backupUrl = 'https://quickchart.io/qr?size=300&margin=1&text=' . urlencode($profileUrl);
                $qrCodeData = self::downloadData($backupUrl);
            }

            if (!$qrCodeData) {
                error_log("Failed to download QR code data for user $userId from both APIs");
                return null;
            }
            
            // Save QR code
            $filename = 'qr_' . $userId . '_' . time() . '.png';
            $filepath = self::$qrCodeDir . $filename;
            
            if (file_put_contents($filepath, $qrCodeData) === false) {
                error_log("Failed to write QR code file for user $userId at $filepath");
                return null;
            }
            
            return $filename;
            
        } catch (\Exception $e) {
            error_log("QR code generation error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper to download data using cURL or file_get_contents
     */
    private static function downloadData($url)
    {
        // Try cURL first (Best for production servers)
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL check to avoid cert issues
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $data) {
                return $data;
            }
        }

        // Fallback to file_get_contents
        if (ini_get('allow_url_fopen')) {
            return @file_get_contents($url);
        }

        return null;
    }
    
    /**
     * Get QR code path
     */
    public static function getQRCodePath(string $filename): string
    {
        return self::$qrCodeDir . $filename;
    }
    
    /**
     * Get QR code URL
     */
    public static function getQRCodeUrl(string $filename): string
    {
        // Check if file exists in storage
        $storagePath = self::$qrCodeDir . $filename;
        if (file_exists($storagePath) && filesize($storagePath) > 0) {
            // Load URL helper if not available
            if (!function_exists('url')) {
                require_once __DIR__ . '/UrlHelper.php';
            }
            // Return URL through serving route
            return url('qrcode/' . $filename);
        }
        return '';
    }
    
    /**
     * Delete QR code
     */
    public static function deleteQRCode(string $filename): bool
    {
        $filepath = self::$qrCodeDir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return true;
    }
    
    /**
     * Regenerate QR code for a profile
     */
    public static function regenerateQRCode(int $userId, string $profileSlug, ?string $oldFilename = null): ?string
    {
        // Delete old QR code if exists
        if ($oldFilename) {
            self::deleteQRCode($oldFilename);
        }
        
        // Generate new QR code
        return self::generateProfileQRCode($userId, $profileSlug);
    }
}