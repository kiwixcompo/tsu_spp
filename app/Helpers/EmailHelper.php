<?php

namespace App\Helpers;

// PHPMailer classes - will be loaded if available

class EmailHelper
{
    private $config;
    private $mailer;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
        $this->setupMailer();
    }

   private function setupMailer(): void
{
    // Check if PHPMailer is available
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        $this->mailer = null;
        error_log("PHPMailer not found - using PHP mail() function as fallback");
        return;
    }
    
    try {
        $smtpConfig = $this->config['mailers']['smtp'];
        
        // Check if SMTP is properly configured
        if (empty($smtpConfig['host']) || empty($smtpConfig['username']) || empty($smtpConfig['password'])) {
            error_log("SMTP not configured - using PHP mail() function as fallback");
            $this->mailer = null;
            return;
        }
        
        $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Enable verbose debug output (only in debug mode)
        if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
            $this->mailer->SMTPDebug = 2;
            $this->mailer->Debugoutput = function($str, $level) {
                error_log("SMTP Debug [$level]: $str");
            };
        }
        
        // Server settings - Google Workspace SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = $smtpConfig['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $smtpConfig['username'];
        $this->mailer->Password = $smtpConfig['password'];
        
        // Use STARTTLS encryption for port 587
        if ($smtpConfig['port'] == 587) {
            $this->mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $this->mailer->SMTPSecure = $smtpConfig['encryption'];
        }
        
        $this->mailer->Port = $smtpConfig['port'];
        
        // SSL options for cPanel/Google Workspace compatibility
        $this->mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => false
            )
        );
        
        $this->mailer->Timeout = 30;
        $this->mailer->SMTPKeepAlive = false;
        
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Encoding = 'base64';
        
        $this->mailer->setFrom(
            $this->config['from']['address'],
            $this->config['from']['name']
        );
        
        $this->mailer->isHTML(true);
        
        error_log("PHPMailer configured successfully with Google Workspace SMTP: " . $smtpConfig['host'] . ":" . $smtpConfig['port']);
        
    } catch (\Exception $e) {
        error_log("PHPMailer setup failed: " . $e->getMessage());
        $this->mailer = null;
    }
}

   public function sendVerificationEmail(string $email, string $code): bool
{
    error_log("=== SENDING VERIFICATION EMAIL ===");
    error_log("To: $email");
    error_log("Code: $code");
    error_log("Mailer is null: " . ($this->mailer === null ? 'YES' : 'NO'));
    
    try {
        // Save verification code to file for easy access
        $this->saveVerificationCode($email, $code);
        
        $subject = 'Verify Your TSU Staff Portal Account';
        $body = $this->getEmailTemplate('verification', [
            'code' => $code,
            'email' => $email,
        ]);
        
        error_log("Email body generated, length: " . strlen($body));
        
        if ($this->mailer === null) {
            // Use simple mail() function as fallback
            error_log("Using mail() function to send verification email to: $email");
            $result = $this->sendSimpleEmail($email, $subject, $body);
            error_log("mail() result: " . ($result ? 'SUCCESS' : 'FAILED'));
            return $result;
        }
        
        // Use PHPMailer
        error_log("Using PHPMailer to send verification email to: $email");
        
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($email);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;
        $this->mailer->AltBody = strip_tags($body);
        
        $result = $this->mailer->send();
        
        if ($result) {
            error_log("✓ PHPMailer: Verification email sent successfully to: $email");
        } else {
            error_log("✗ PHPMailer: Failed to send verification email to: $email");
        }
        
        return $result;
        
    } catch (\Exception $e) {
        error_log("Email sending exception: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Fallback to simple mail
        try {
            error_log("Attempting fallback to mail() function");
            return $this->sendSimpleEmail($email, $subject ?? 'Verify Your Account', $body ?? '');
        } catch (\Exception $e2) {
            error_log("Fallback email also failed: " . $e2->getMessage());
        }
        
        return true; // Return true so registration continues
    }
}

    public function sendPasswordResetEmail(string $email, string $token): bool
    {
        try {
            $resetUrl = ($_ENV['APP_URL'] ?? 'https://staff.tsuniversity.edu.ng') . '/reset-password?token=' . $token;
            
            // Save reset link to file
            $this->savePasswordResetLink($email, $resetUrl);
            
            $subject = 'Reset Your TSU Staff Portal Password';
            $body = $this->getEmailTemplate('password_reset', [
                'reset_url' => $resetUrl,
                'email' => $email,
            ]);
            
            if ($this->mailer === null) {
                // Use simple mail() function as fallback
                return $this->sendSimpleEmail($email, $subject, $body);
            }
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            $result = $this->mailer->send();
            
            if ($result) {
                error_log("✓ Password reset email sent successfully to: $email");
            }
            
            return $result;
            
        } catch (\Exception $e) {
            error_log("Password reset email failed: " . $e->getMessage());
            
            // Try fallback
            try {
                return $this->sendSimpleEmail($email, $subject ?? 'Reset Password', $body ?? '');
            } catch (\Exception $e2) {
                error_log("Fallback password reset email also failed: " . $e2->getMessage());
            }
            
            return false;
        }
    }

    public function sendProfileUpdateNotification(string $email, string $profileUrl): bool
    {
        try {
            $subject = 'Your TSU Staff Profile Has Been Updated';
            $body = $this->getEmailTemplate('profile_update', [
                'profile_url' => $profileUrl,
                'email' => $email,
            ]);
            
            if ($this->mailer === null) {
                // Use simple mail() function as fallback
                return $this->sendSimpleEmail($email, $subject, $body);
            }
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            return $this->mailer->send();
            
        } catch (\Exception $e) {
            error_log("Profile update email failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Simple email system - uses PHP mail() function
     */
    private function sendSimpleEmail(string $to, string $subject, string $body): bool
    {
        // Save to file for backup/debugging
        $this->saveEmailForDevelopment($to, $subject, $body);
        
        // Extract and save verification codes
        if (strpos($subject, 'Verify') !== false) {
            preg_match('/(\d{6})/', strip_tags($body), $matches);
            if (!empty($matches[1])) {
                $this->saveVerificationCode($to, $matches[1]);
            }
        }
        
        // Extract and save password reset links
        if (strpos($subject, 'Reset') !== false || strpos($subject, 'Password') !== false) {
            preg_match('/https?:\/\/[^\s<>"]+reset-password[^\s<>"]*/', $body, $matches);
            if (!empty($matches[0])) {
                $this->savePasswordResetLink($to, $matches[0]);
            }
        }
        
        // Send actual email using PHP mail() function
        try {
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . $this->config['from']['name'] . " <" . $this->config['from']['address'] . ">" . "\r\n";
            $headers .= "Reply-To: " . $this->config['from']['address'] . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            // Add detailed logging
            error_log("=== EMAIL DEBUG (mail function) ===");
            error_log("To: $to");
            error_log("From: " . $this->config['from']['address']);
            error_log("Subject: $subject");
            error_log("Mail Host (from .env): " . ($_ENV['MAIL_HOST'] ?? 'not set'));
            error_log("Mail Port (from .env): " . ($_ENV['MAIL_PORT'] ?? 'not set'));
            error_log("Mail Username (from .env): " . ($_ENV['MAIL_USERNAME'] ?? 'not set'));
            error_log("===================================");
            
            // Send email
            $result = @mail($to, $subject, $body, $headers);
            
            if ($result) {
                error_log("✓ mail() function: Email sent successfully to: $to");
            } else {
                error_log("✗ mail() function: Email sending failed to: $to");
                $lastError = error_get_last();
                if ($lastError) {
                    error_log("Last PHP error: " . $lastError['message']);
                }
            }
            
            return $result;
            
        } catch (\Exception $e) {
            error_log("Email exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save email to file for development testing
     */
    private function saveEmailForDevelopment(string $to, string $subject, string $body): void
    {
        try {
            $emailDir = __DIR__ . '/../../storage/emails';
            if (!is_dir($emailDir)) {
                mkdir($emailDir, 0755, true);
            }
            
            $filename = $emailDir . '/' . date('Y-m-d_H-i-s') . '_' . md5($to . $subject) . '.html';
            
            $emailContent = "
            <div style='background: #f0f0f0; padding: 20px; margin-bottom: 20px; border-radius: 5px;'>
                <h3>📧 Email System Debug</h3>
                <p><strong>To:</strong> {$to}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Sent:</strong> " . date('Y-m-d H:i:s') . "</p>
                <p><strong>Type:</strong> " . $this->getEmailType($subject) . "</p>
                <p><strong>Method:</strong> " . ($this->mailer ? 'PHPMailer/SMTP' : 'PHP mail()') . "</p>
                <p><strong>SMTP Host:</strong> " . ($_ENV['MAIL_HOST'] ?? 'not configured') . "</p>
                <p><strong>SMTP Port:</strong> " . ($_ENV['MAIL_PORT'] ?? 'not configured') . "</p>
            </div>
            {$body}
            ";
            
            file_put_contents($filename, $emailContent);
            error_log("Email saved to file: $filename");
            
        } catch (\Exception $e) {
            error_log("Failed to save email to file: " . $e->getMessage());
        }
    }

    /**
     * Save verification code for easy access
     */
    private function saveVerificationCode(string $email, string $code): void
    {
        try {
            $codesFile = __DIR__ . '/../../public/verification_codes.txt';
            $entry = date('Y-m-d H:i:s') . " | {$email} | {$code}\n";
            file_put_contents($codesFile, $entry, FILE_APPEND | LOCK_EX);
            error_log("Verification code saved to file: $code for $email");
        } catch (\Exception $e) {
            error_log("Failed to save verification code: " . $e->getMessage());
        }
    }

    /**
     * Save password reset link for easy access
     */
    private function savePasswordResetLink(string $email, string $link): void
    {
        try {
            $linksFile = __DIR__ . '/../../public/reset_links.txt';
            $entry = date('Y-m-d H:i:s') . " | {$email} | {$link}\n";
            file_put_contents($linksFile, $entry, FILE_APPEND | LOCK_EX);
            error_log("Password reset link saved to file for: $email");
        } catch (\Exception $e) {
            error_log("Failed to save reset link: " . $e->getMessage());
        }
    }

    /**
     * Get email type for display
     */
    private function getEmailType(string $subject): string
    {
        if (strpos($subject, 'Verify') !== false) return '🔐 Email Verification';
        if (strpos($subject, 'Reset') !== false || strpos($subject, 'Password') !== false) return '🔑 Password Reset';
        if (strpos($subject, 'Profile') !== false) return '👤 Profile Update';
        return '📨 General';
    }

    private function getEmailTemplate(string $template, array $data): string
    {
        switch ($template) {
            case 'verification':
                return $this->getVerificationTemplate($data);
            case 'password_reset':
                return $this->getPasswordResetTemplate($data);
            case 'profile_update':
                return $this->getProfileUpdateTemplate($data);
            default:
                throw new \Exception("Unknown email template: {$template}");
        }
    }

    private function getVerificationTemplate(array $data): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Verify Your Account</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1e40af; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px 20px; background: #f9fafb; }
                .code { font-size: 32px; font-weight: bold; color: #1e40af; text-align: center; 
                        background: white; padding: 20px; border-radius: 8px; margin: 20px 0; 
                        letter-spacing: 4px; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>TSU Staff Portal</h1>
                    <p>Verify Your Account</p>
                </div>
                <div class='content'>
                    <h2>Welcome to TSU Staff Portal!</h2>
                    <p>Thank you for registering with the Taraba State University Staff Profile Portal. 
                       To complete your registration, please use the verification code below:</p>
                    
                    <div class='code'>{$data['code']}</div>
                    
                    <p>This code will expire in 24 hours. If you didn't create an account, 
                       please ignore this email.</p>
                    
                    <p>Enter this code on the verification page to activate your account and 
                       start building your professional profile.</p>
                </div>
                <div class='footer'>
                    <p>Best regards,<br>TSU Staff Portal Team</p>
                    <p>Taraba State University<br>
                       Jalingo, Taraba State, Nigeria</p>
                </div>
            </div>
        </body>
        </html>";
    }

    private function getPasswordResetTemplate(array $data): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Reset Your Password</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1e40af; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px 20px; background: #f9fafb; }
                .button { display: inline-block; padding: 12px 24px; background: #1e40af; 
                         color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>TSU Staff Portal</h1>
                    <p>Password Reset Request</p>
                </div>
                <div class='content'>
                    <h2>Reset Your Password</h2>
                    <p>We received a request to reset your password for your TSU Staff Portal account.</p>
                    
                    <p>Click the button below to reset your password:</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$data['reset_url']}' class='button'>Reset Password</a>
                    </div>
                    
                    <p>This link will expire in 1 hour for security reasons.</p>
                    
                    <p>If you didn't request a password reset, please ignore this email. 
                       Your password will remain unchanged.</p>
                       
                    <p><strong>For security:</strong> Never share this link with anyone.</p>
                </div>
                <div class='footer'>
                    <p>Best regards,<br>TSU Staff Portal Team</p>
                    <p>If you're having trouble clicking the button, copy and paste this URL into your browser:<br>
                       <small>{$data['reset_url']}</small></p>
                </div>
            </div>
        </body>
        </html>";
    }

    private function getProfileUpdateTemplate(array $data): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Profile Updated</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1e40af; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px 20px; background: #f9fafb; }
                .button { display: inline-block; padding: 12px 24px; background: #1e40af; 
                         color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>TSU Staff Portal</h1>
                    <p>Profile Update Confirmation</p>
                </div>
                <div class='content'>
                    <h2>Your Profile Has Been Updated</h2>
                    <p>This is to confirm that your TSU Staff Portal profile has been successfully updated.</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$data['profile_url']}' class='button'>View Your Profile</a>
                    </div>
                    
                    <p>If you didn't make these changes, please contact support immediately.</p>
                </div>
                <div class='footer'>
                    <p>Best regards,<br>TSU Staff Portal Team</p>
                </div>
            </div>
        </body>
        </html>";
    }
}