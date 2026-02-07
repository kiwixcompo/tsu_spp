<?php

namespace App\Core;

/**
 * Error Logger
 * Centralized error logging system
 */
class ErrorLogger
{
    private static $logFile = __DIR__ . '/../../error.log';
    
    /**
     * Initialize error logging
     */
    public static function init(): void
    {
        // Set error handler
        set_error_handler([self::class, 'handleError']);
        
        // Set exception handler
        set_exception_handler([self::class, 'handleException']);
        
        // Set shutdown handler for fatal errors
        register_shutdown_function([self::class, 'handleShutdown']);
        
        // Ensure log file exists and is writable
        self::ensureLogFile();
    }
    
    /**
     * Ensure log file exists
     */
    private static function ensureLogFile(): void
    {
        if (!file_exists(self::$logFile)) {
            file_put_contents(self::$logFile, "=== TSU Staff Profile Error Log ===\n");
            chmod(self::$logFile, 0666);
        }
    }
    
    /**
     * Handle errors
     */
    public static function handleError($errno, $errstr, $errfile, $errline): bool
    {
        $errorTypes = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE ERROR',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE ERROR',
            E_CORE_WARNING => 'CORE WARNING',
            E_COMPILE_ERROR => 'COMPILE ERROR',
            E_COMPILE_WARNING => 'COMPILE WARNING',
            E_USER_ERROR => 'USER ERROR',
            E_USER_WARNING => 'USER WARNING',
            E_USER_NOTICE => 'USER NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER DEPRECATED',
        ];
        
        $type = $errorTypes[$errno] ?? 'UNKNOWN';
        
        self::log($type, $errstr, $errfile, $errline);
        
        // Don't execute PHP internal error handler
        return true;
    }
    
    /**
     * Handle exceptions
     */
    public static function handleException($exception): void
    {
        self::log(
            'EXCEPTION',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        
        // Display user-friendly error page
        if (!headers_sent()) {
            http_response_code(500);
            echo self::getErrorPage();
        }
    }
    
    /**
     * Handle shutdown (fatal errors)
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::log(
                'FATAL ERROR',
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }
    
    /**
     * Log error to file
     */
    public static function log(string $type, string $message, string $file = '', int $line = 0, string $trace = ''): void
    {
        self::ensureLogFile();
        
        $timestamp = date('Y-m-d H:i:s');
        $url = $_SERVER['REQUEST_URI'] ?? 'N/A';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'N/A';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
        
        $logEntry = "\n";
        $logEntry .= "========================================\n";
        $logEntry .= "[$timestamp] $type\n";
        $logEntry .= "========================================\n";
        $logEntry .= "Message: $message\n";
        if ($file) $logEntry .= "File: $file\n";
        if ($line) $logEntry .= "Line: $line\n";
        $logEntry .= "URL: $method $url\n";
        $logEntry .= "IP: $ip\n";
        if ($trace) {
            $logEntry .= "Stack Trace:\n$trace\n";
        }
        $logEntry .= "========================================\n";
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }
    
    /**
     * Get error page HTML
     */
    private static function getErrorPage(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - TSU Staff Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>⚠️ Something Went Wrong</h1>
        <p>We encountered an error while processing your request. The error has been logged and will be reviewed by our team.</p>
        <p>Please try again later or contact support if the problem persists.</p>
        <a href="/" class="btn">Return to Home</a>
    </div>
</body>
</html>';
    }
}
