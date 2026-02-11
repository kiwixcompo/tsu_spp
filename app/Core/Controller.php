<?php

namespace App\Core;

class Controller
{
    protected $db;
    protected $config;

    public function __construct()
    {
        try {
            $this->db = Database::getInstance();
        } catch (\Exception $e) {
            // Database not available, set to null
            $this->db = null;
        }
        $this->config = require __DIR__ . '/../../config/app.php';
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . "/../Views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }
        
        include $viewPath;
    }

    protected function json(array $data, int $status = 200): void
    {
        // Clean any output buffers to prevent HTML/errors before JSON
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        // If URL doesn't start with http, use URL helper
        if (!preg_match('/^https?:\/\//', $url)) {
            // Load URL helper if not available
            if (!function_exists('url')) {
                require_once __DIR__ . '/../Helpers/UrlHelper.php';
            }
            
            // Remove leading slash and use URL helper
            $path = ltrim($url, '/');
            $url = url($path);
        }
        
        header("Location: {$url}");
        exit;
    }

    protected function input(string $key, $default = null)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'GET') {
            return $_GET[$key] ?? $default;
        }
        
        if ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
            // Handle JSON input
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $json = json_decode(file_get_contents('php://input'), true);
                return $json[$key] ?? $default;
            }
            
            return $_POST[$key] ?? $default;
        }
        
        return $default;
    }

    protected function validate(array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $this->input($field);
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $singleRule) {
                $error = $this->validateField($field, $value, $singleRule, $rule);
                if ($error) {
                    $errors[$field] = $error;
                    break;
                }
            }
        }
        
        return $errors;
    }

    private function validateField(string $field, $value, string $rule, string $allRules = ''): ?string
    {
        if ($rule === 'required' && empty($value)) {
            return ucfirst($field) . ' is required';
        }
        
        if ($rule === 'integer' && !empty($value) && !ctype_digit($value)) {
            return ucfirst($field) . ' must be a valid number';
        }
        
        if ($rule === 'date' && !empty($value) && !strtotime($value)) {
            return ucfirst($field) . ' must be a valid date';
        }
        
        if (strpos($rule, 'min:') === 0) {
            $min = (int)substr($rule, 4);
            
            // Check if this is an integer field by looking for 'integer' in all rules
            $isIntegerField = strpos($allRules, 'integer') !== false;
            
            if ($isIntegerField) {
                // For integer fields, validate the numeric value
                if (!empty($value) && (int)$value < $min) {
                    return ucfirst($field) . " must be at least {$min}";
                }
            } else {
                // For string fields, validate the length
                if (strlen($value) < $min) {
                    return ucfirst($field) . " must be at least {$min} characters";
                }
            }
        }
        
        if (strpos($rule, 'max:') === 0) {
            $max = (int)substr($rule, 4);
            
            // Check if this is an integer field
            $isIntegerField = strpos($allRules, 'integer') !== false;
            
            if ($isIntegerField) {
                // For integer fields, validate the numeric value
                if (!empty($value) && (int)$value > $max) {
                    return ucfirst($field) . " must not exceed {$max}";
                }
            } else {
                // For string fields, validate the length
                if (strlen($value) > $max) {
                    return ucfirst($field) . " must not exceed {$max} characters";
                }
            }
        }
        
        if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return ucfirst($field) . ' must be a valid email address';
        }
        
        if ($rule === 'tsu_email' && !preg_match('/^[a-zA-Z0-9._-]+@tsuniversity\.edu\.ng$/', $value)) {
            return ucfirst($field) . ' must be a valid TSU email address';
        }
        
        if ($rule === 'password' && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $value)) {
            return ucfirst($field) . ' must contain at least 8 characters with uppercase, lowercase, and number';
        }
        
        if (strpos($rule, 'matches:') === 0) {
            $matchField = substr($rule, 8);
            $matchValue = $this->input($matchField);
            if ($value !== $matchValue) {
                return ucfirst($field) . ' must match ' . ucfirst($matchField);
            }
        }
        
        if ($rule === 'password_strength' && !empty($value)) {
            if (strlen($value) < 8) {
                return ucfirst($field) . ' must be at least 8 characters long';
            }
            if (!preg_match('/[A-Z]/', $value)) {
                return ucfirst($field) . ' must contain at least one uppercase letter';
            }
            if (!preg_match('/[a-z]/', $value)) {
                return ucfirst($field) . ' must contain at least one lowercase letter';
            }
            if (!preg_match('/\d/', $value)) {
                return ucfirst($field) . ' must contain at least one number';
            }
            if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
                return ucfirst($field) . ' must contain at least one special character';
            }
        }
        
        return null;
    }

    protected function generateCSRFToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCSRFToken(): bool
    {
        // Check common locations for CSRF token: form field, JSON body, and X-CSRF-Token header
        $token = $this->input('csrf_token');

        // Check JSON payload if not present
        if (empty($token)) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $json = json_decode(file_get_contents('php://input'), true);
                if (is_array($json) && isset($json['csrf_token'])) {
                    $token = $json['csrf_token'];
                }
            }
        }

        // Check header (fetch sets X-CSRF-Token)
        if (empty($token)) {
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            $headerToken = null;
            if (!empty($headers)) {
                foreach ($headers as $k => $v) {
                    if (strtolower($k) === 'x-csrf-token' || strtolower($k) === 'x-csrf') {
                        $headerToken = $v;
                        break;
                    }
                }
            }
            // Also check server vars for older setups
            if (!$headerToken && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
            }

            if ($headerToken) {
                $token = $headerToken;
            }
        }

        return isset($_SESSION['csrf_token']) && !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
    }

    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    protected function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return $this->db->fetch(
            "SELECT u.*, p.first_name, p.last_name, p.profile_photo, p.profile_slug 
             FROM users u 
             LEFT JOIN profiles p ON u.id = p.user_id 
             WHERE u.id = ?",
            [$_SESSION['user_id']]
        );
    }

    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }
    }

protected function sanitizeInput(?string $input): string
{
    if ($input === null) {
        return '';
    }
    return trim(strip_tags($input));
}

    protected function generateSlug(string $text): string
    {
        $slug = strtolower($text);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }

    protected function logActivity(string $action, array $details = []): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
