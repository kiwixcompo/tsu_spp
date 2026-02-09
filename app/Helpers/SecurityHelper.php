<?php

namespace App\Helpers;

/**
 * Security Helper
 * Provides security functions to protect against malware, XSS, SQL injection, etc.
 */
class SecurityHelper
{
    private static $config;
    
    /**
     * Initialize security configuration
     */
    private static function loadConfig()
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/security.php';
        }
        return self::$config;
    }
    
    /**
     * Validate file upload for security threats
     * 
     * @param array $file $_FILES array element
     * @param string $type 'photo' or 'document'
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validateFileUpload($file, $type = 'photo')
    {
        $config = self::loadConfig();
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'Invalid file upload'];
        }
        
        // Get file extension
        $filename = $file['name'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Check for blocked extensions
        if (in_array($extension, $config['upload']['blocked_extensions'])) {
            self::logSuspiciousActivity('Blocked file extension attempted: ' . $extension);
            return ['valid' => false, 'error' => 'File type not allowed for security reasons'];
        }
        
        // Check allowed extensions
        $allowedExtensions = $type === 'photo' 
            ? $config['upload']['allowed_photo_extensions']
            : $config['upload']['allowed_document_extensions'];
            
        if (!in_array($extension, $allowedExtensions)) {
            return ['valid' => false, 'error' => 'File type not allowed'];
        }
        
        // Check file size
        $maxSize = $type === 'photo'
            ? $config['upload']['max_photo_size']
            : $config['upload']['max_document_size'];
            
        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'File size exceeds maximum allowed'];
        }
        
        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = $type === 'photo'
            ? $config['upload']['allowed_photo_mimes']
            : $config['upload']['allowed_document_mimes'];
            
        if (!in_array($mimeType, $allowedMimes)) {
            self::logSuspiciousActivity('Invalid MIME type: ' . $mimeType . ' for file: ' . $filename);
            return ['valid' => false, 'error' => 'File type validation failed'];
        }
        
        // Check for PHP code in image files
        if ($type === 'photo') {
            $content = file_get_contents($file['tmp_name']);
            if (preg_match('/<\?php|<\?=|<script/i', $content)) {
                self::logSuspiciousActivity('PHP/Script code detected in image file: ' . $filename);
                return ['valid' => false, 'error' => 'File contains malicious code'];
            }
        }
        
        // Check for double extensions (e.g., file.php.jpg)
        $parts = explode('.', $filename);
        if (count($parts) > 2) {
            foreach (array_slice($parts, 0, -1) as $part) {
                if (in_array(strtolower($part), $config['upload']['blocked_extensions'])) {
                    self::logSuspiciousActivity('Double extension detected: ' . $filename);
                    return ['valid' => false, 'error' => 'Invalid filename'];
                }
            }
        }
        
        return ['valid' => true, 'error' => null];
    }
    
    /**
     * Sanitize input to prevent XSS
     * 
     * @param string $input
     * @param bool $allowHtml
     * @return string
     */
    public static function sanitizeInput($input, $allowHtml = false)
    {
        if (is_array($input)) {
            return array_map(function($item) use ($allowHtml) {
                return self::sanitizeInput($item, $allowHtml);
            }, $input);
        }
        
        $config = self::loadConfig();
        
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        if ($allowHtml) {
            // Strip dangerous tags but allow safe ones
            $input = strip_tags($input, $config['input']['allowed_tags']);
        } else {
            // Strip all HTML tags
            $input = strip_tags($input);
        }
        
        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $input;
    }
    
    /**
     * Validate and sanitize email
     * 
     * @param string $email
     * @return string|false
     */
    public static function sanitizeEmail($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
    }
    
    /**
     * Validate and sanitize URL
     * 
     * @param string $url
     * @return string|false
     */
    public static function sanitizeUrl($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : false;
    }
    
    /**
     * Generate secure random token
     * 
     * @param int $length
     * @return string
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Set security headers
     */
    public static function setSecurityHeaders()
    {
        $config = self::loadConfig();
        
        foreach ($config['headers'] as $header => $value) {
            header("$header: $value");
        }
        
        // Prevent caching of sensitive pages
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
    
    /**
     * Check rate limiting
     * 
     * @param string $action
     * @param string $identifier (IP or user ID)
     * @return bool
     */
    public static function checkRateLimit($action, $identifier)
    {
        $config = self::loadConfig();
        
        // Implementation would use session or database to track attempts
        // For now, return true (allow)
        return true;
    }
    
    /**
     * Log suspicious activity
     * 
     * @param string $message
     */
    private static function logSuspiciousActivity($message)
    {
        $config = self::loadConfig();
        
        if ($config['logging']['log_suspicious_activity']) {
            $logFile = __DIR__ . '/../../storage/logs/security.log';
            $logDir = dirname($logFile);
            
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            $timestamp = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            $logMessage = "[$timestamp] IP: $ip | User-Agent: $userAgent | $message\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
    }
    
    /**
     * Validate password strength
     * 
     * @param string $password
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validatePassword($password)
    {
        $config = self::loadConfig();
        $errors = [];
        
        if (strlen($password) < $config['password']['min_length']) {
            $errors[] = 'Password must be at least ' . $config['password']['min_length'] . ' characters';
        }
        
        if ($config['password']['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if ($config['password']['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if ($config['password']['require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if ($config['password']['require_special_chars'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Prevent SQL injection by escaping string
     * 
     * @param string $string
     * @return string
     */
    public static function escapeSql($string)
    {
        return addslashes($string);
    }
    
    /**
     * Check if IP is blocked
     * 
     * @param string $ip
     * @return bool
     */
    public static function isIpBlocked($ip)
    {
        $config = self::loadConfig();
        
        if (!$config['ip_blocking']['enabled']) {
            return false;
        }
        
        return in_array($ip, $config['ip_blocking']['blocked_ips']);
    }
    
    /**
     * Sanitize filename
     * 
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename($filename)
    {
        // Remove path information
        $filename = basename($filename);
        
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple dots (except before extension)
        $parts = explode('.', $filename);
        if (count($parts) > 2) {
            $extension = array_pop($parts);
            $filename = implode('_', $parts) . '.' . $extension;
        }
        
        return $filename;
    }
}
