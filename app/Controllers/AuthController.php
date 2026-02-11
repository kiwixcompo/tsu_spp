<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Helpers\EmailHelper;

class AuthController extends Controller
{
    private $userModel;
    private $profileModel;
    private $emailHelper;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->emailHelper = new EmailHelper();
        
        try {
            $this->profileModel = new Profile();
        } catch (\Exception $e) {
            $this->profileModel = null;
        }
    }

    public function showRegister(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }

        $this->view('auth/register', [
            'csrf_token' => $this->generateCSRFToken(),
        ]);
    }

    public function register(): void
    {
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        // Get staff type and other inputs first
        $staffType = $this->sanitizeInput($this->input('staff_type')) ?: 'teaching';
        $emailPrefix = $this->sanitizeInput($this->input('email_prefix'));
        $password = $this->input('password');
        $confirmPassword = $this->input('confirm_password');
        $staffPrefix = $this->sanitizeInput($this->input('staff_prefix'));
        $staffNumber = $this->sanitizeInput($this->input('staff_number'));
        $profileVisibility = $this->sanitizeInput($this->input('profile_visibility')) ?: 'public';
        
        // Initialize errors array
        $errors = [];
        
        // Validate common fields
        if (empty($emailPrefix) || strlen($emailPrefix) < 3) {
            $errors['email_prefix'] = 'Email prefix is required (minimum 3 characters)';
        } elseif (!preg_match('/^[a-zA-Z0-9._-]+$/', $emailPrefix)) {
            $errors['email_prefix'] = 'Email prefix can only contain letters, numbers, dots, and hyphens';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }
        
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        // Validate staff ID
        if (empty($staffPrefix) || !in_array($staffPrefix, ['TSU/SP/', 'TSU/JP/'], true)) {
            $errors['staff_prefix'] = 'Please select a valid staff ID prefix (TSU/SP/ or TSU/JP/)';
        }
        if (empty($staffNumber) || !preg_match('/^[0-9]+$/', $staffNumber)) {
            $errors['staff_number'] = 'Staff number should contain numbers only';
        }
        
        // Conditional validation based on staff type
        if ($staffType === 'teaching') {
            // Teaching staff: faculty and department are required
            $faculty = $this->sanitizeInput($this->input('faculty'));
            $department = $this->sanitizeInput($this->input('department'));
            $unit = null;
            
            if (empty($faculty)) {
                $errors['faculty'] = 'Faculty is required for teaching staff';
            }
            if (empty($department)) {
                $errors['department'] = 'Department is required for teaching staff';
            }
        } else {
            // Non-teaching staff: either unit OR (faculty + department) required
            $unit = $this->sanitizeInput($this->input('unit'));
            $faculty = $this->sanitizeInput($this->input('faculty_nt'));
            $department = $this->sanitizeInput($this->input('department_nt'));
            
            // Check if at least one option is selected
            if (empty($unit) && empty($faculty) && empty($department)) {
                $errors['staff_location'] = 'Please select either a Unit/Office OR Faculty/Department';
            }
            
            // If faculty is selected, department must also be selected
            if (!empty($faculty) && empty($department)) {
                $errors['department'] = 'Please select a department for the selected faculty';
            }
        }

        $email = $emailPrefix . '@tsuniversity.edu.ng';
        
        // Check if email already exists
        if ($this->userModel->findByEmail($email)) {
            $errors['email_prefix'] = 'This email is already registered';
        }

        // Check if staff number already exists (prefix + number combination must be unique)
        if (!empty($staffPrefix) && !empty($staffNumber)) {
            $fullStaffNumber = $staffPrefix . $staffNumber;
            try {
                $existingStaff = $this->db->fetch(
                    "SELECT id FROM profiles WHERE staff_number = ?",
                    [$fullStaffNumber]
                );
                if ($existingStaff) {
                    $errors['staff_number'] = 'This staff number is already registered';
                }
            } catch (\Exception $e) {
                error_log("Staff number check failed: " . $e->getMessage());
            }
        }

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        try {
            // Generate verification code
            $verificationCode = sprintf('%06d', mt_rand(100000, 999999));
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Create user
            $userId = $this->userModel->create([
                'email' => $email,
                'email_prefix' => $emailPrefix,
                'password' => $password,
                'verification_code' => $verificationCode,
                'verification_expires' => $verificationExpires,
            ]);

            // Send verification email
                error_log("About to send verification email to: $email with code: $verificationCode");
                try {
                    $emailResult = $this->emailHelper->sendVerificationEmail($email, $verificationCode);
                    error_log("Email send result: " . ($emailResult ? 'SUCCESS' : 'FAILED'));
                } catch (\Exception $e) {
                    error_log("Email send exception in controller: " . $e->getMessage());
                }
                error_log("Finished email sending attempt");

            // Store registration data for profile creation
            $_SESSION['registration_data'] = [
                'staff_type' => $staffType,
                'faculty' => $faculty ?? null,
                'department' => $department ?? null,
                'unit' => $unit ?? null,
                'staff_number' => $staffPrefix . $staffNumber,
                'profile_visibility' => $profileVisibility,
            ];

            // Log activity
            $this->logActivity('user_registered', ['email' => $email]);

            $_SESSION['pending_verification'] = $userId;

            $this->json([
                'success' => true,
                'message' => 'Registration successful. Please check your email for verification code.',
                'redirect' => 'verify-email?email=' . urlencode($email),
            ]);

        } catch (\Exception $e) {
            $this->json(['error' => 'Registration failed. Please try again.'], 500);
        }
    }

    public function showVerifyEmail(): void
    {
        // Check if user is already logged in
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
            return;
        }

        // If no pending verification, check if there's a user parameter
        if (!isset($_SESSION['pending_verification'])) {
            $email = $this->input('email');
            if ($email) {
                // Find user by email and set pending verification
                $user = $this->userModel->findByEmail($email);
                if ($user && !$user['email_verified']) {
                    $_SESSION['pending_verification'] = $user['id'];
                } else {
                    $this->redirect('register');
                    return;
                }
            } else {
                $this->redirect('register');
                return;
            }
        }

        $this->view('auth/verify-email', [
            'csrf_token' => $this->generateCSRFToken(),
        ]);
    }

    public function verifyEmail(): void
    {
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $code = $this->sanitizeInput($this->input('verification_code'));
        $email = $this->sanitizeInput($this->input('email')); // Allow email as fallback

        if (empty($code) || !preg_match('/^\d{6}$/', $code)) {
            $this->json(['error' => 'Please enter a valid 6-digit code'], 400);
            return;
        }

        // Try to get user ID from session first, then from email
        $userId = $_SESSION['pending_verification'] ?? null;
        $user = null;

        if ($userId) {
            $user = $this->userModel->findById($userId);
        } elseif ($email) {
            $user = $this->userModel->findByEmail($email);
            $userId = $user['id'] ?? null;
        } else {
            // Try to find user by verification code as last resort
            $user = $this->userModel->findByVerificationCode($code);
            $userId = $user['id'] ?? null;
        }

        if (!$user) {
            $this->json(['error' => 'User not found. Please try registering again.'], 404);
            return;
        }

        // Check if already verified
        if ($user['email_verified']) {
            $this->json(['error' => 'Email is already verified. Please login.'], 400);
            return;
        }

        // Check if code matches and hasn't expired
        if ($user['verification_code'] !== $code) {
            $this->json(['error' => 'Invalid verification code'], 400);
            return;
        }

        if (strtotime($user['verification_expires']) < time()) {
            $this->json(['error' => 'Verification code has expired. Please request a new one.'], 400);
            return;
        }

        try {
            // Verify email and activate account
            $this->userModel->verifyEmail($userId);

            // Log user in
            $_SESSION['user_id'] = $userId;
            unset($_SESSION['pending_verification']);

            // Log activity
            $this->logActivity('email_verified', ['user_id' => $userId]);

            $this->json([
                'success' => true,
                'message' => 'Email verified successfully!',
                'redirect' => 'profile/setup',
            ]);

        } catch (\Exception $e) {
            error_log("Verification error: " . $e->getMessage());
            $this->json(['error' => 'Verification failed. Please try again.'], 500);
        }
    }

    public function showLogin(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }

        $this->view('auth/login', [
            'csrf_token' => $this->generateCSRFToken(),
        ]);
    }

    public function login(): void
    {
        if (!$this->verifyCSRFToken()) {
            error_log("CSRF validation failed. Session token: " . ($_SESSION['csrf_token'] ?? 'not set') . ", Posted token: " . ($this->input('csrf_token') ?? 'not set'));
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $errors = $this->validate([
            'email' => 'required|tsu_email',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        $email = $this->sanitizeInput($this->input('email'));
        $password = $this->input('password');
        $rememberMe = $this->input('remember_me') === 'on';

        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            $this->json(['error' => 'Invalid email or password'], 401);
            return;
        }

        if ($user['account_status'] === 'suspended') {
            $this->json(['error' => 'Your account has been suspended'], 403);
            return;
        }

        if (!$user['email_verified']) {
            // Generate a new verification code
            $verificationCode = sprintf('%06d', mt_rand(100000, 999999));
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            // Update user with new verification code
            $this->userModel->updateVerificationCode($user['id'], $verificationCode, $verificationExpires);
            
            // Send new verification email
            try {
                $this->emailHelper->sendVerificationEmail($user['email'], $verificationCode);
                $emailSent = true;
            } catch (\Exception $e) {
                error_log("Failed to send verification email: " . $e->getMessage());
                $emailSent = false;
            }
            
            // Set session for verification page
            $_SESSION['pending_verification'] = $user['id'];
            
            $message = $emailSent 
                ? 'Your email is not verified. A new verification code has been sent to your email.'
                : 'Your email is not verified. Please check your email for the verification code.';
            
            $this->json([
                'error' => $message,
                'redirect' => 'verify-email?email=' . urlencode($user['email']) . '&new_code=1',
            ], 403);
            return;
        }

        try {
            // Log user in
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            
            // Update last login
            $this->userModel->updateLastLogin($user['id']);

            // Set remember me cookie if requested
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                // Store token in database (you'd need to add this field)
            }

            // Log activity
            $this->logActivity('user_login', ['user_id' => $user['id']]);

            // Redirect based on user role
            $redirectUrl = 'dashboard'; // Default for regular users
            if ($user['role'] === 'admin') {
                $redirectUrl = 'admin/dashboard';
            } elseif ($user['role'] === 'id_card_manager') {
                $redirectUrl = 'id-card-manager/dashboard';
            }

            $this->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => $redirectUrl,
            ]);

        } catch (\Exception $e) {
            error_log("Login error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            $this->json(['error' => 'Login failed. Please try again.', 'debug' => $e->getMessage()], 500);
        }
    }

    public function logout(): void
    {
        if ($this->isAuthenticated()) {
            $this->logActivity('user_logout');
        }

        // Clear session
        session_destroy();
        
        // Clear remember me cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);

        $this->redirect('');
    }

    public function showForgotPassword(): void
    {
        $this->view('auth/forgot-password', [
            'csrf_token' => $this->generateCSRFToken(),
        ]);
    }

    public function forgotPassword(): void
    {
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $errors = $this->validate([
            'email' => 'required|tsu_email',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        $email = $this->sanitizeInput($this->input('email'));
        $user = $this->userModel->findByEmail($email);

        // Always return success to prevent email enumeration
        $this->json([
            'success' => true,
            'message' => 'If an account with that email exists, a password reset link has been sent.',
        ]);

        if ($user && $user['email_verified']) {
            try {
                $resetToken = bin2hex(random_bytes(32));
                $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $this->userModel->setPasswordResetToken($user['id'], $resetToken, $resetExpires);
                $this->emailHelper->sendPasswordResetEmail($email, $resetToken);

                $this->logActivity('password_reset_requested', ['email' => $email]);
            } catch (\Exception $e) {
                // Log error but don't reveal to user
                error_log("Password reset failed for {$email}: " . $e->getMessage());
            }
        }
    }

    public function resendVerification(): void
    {
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        if (!isset($_SESSION['pending_verification'])) {
            $this->json(['error' => 'No pending verification'], 400);
            return;
        }

        $userId = $_SESSION['pending_verification'];
        $user = $this->userModel->findById($userId);

        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
            return;
        }

        try {
            $verificationCode = sprintf('%06d', mt_rand(100000, 999999));
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Update verification code (not password reset token!)
            $this->userModel->updateVerificationCode($userId, $verificationCode, $verificationExpires);
            
            // Send verification email
            $this->emailHelper->sendVerificationEmail($user['email'], $verificationCode);

            $this->json([
                'success' => true,
                'message' => 'A new verification code has been sent to your email!',
            ]);

        } catch (\Exception $e) {
            error_log("Resend verification failed: " . $e->getMessage());
            $this->json(['error' => 'Failed to send verification code. Please try again.'], 500);
        }
    }

    /**
     * Check if user's email has been verified (for auto-refresh)
     */
    public function checkVerificationStatus(): void
    {
        if (!isset($_SESSION['pending_verification'])) {
            $this->json(['verified' => false, 'error' => 'No pending verification'], 400);
            return;
        }

        $userId = $_SESSION['pending_verification'];
        $user = $this->userModel->findById($userId);

        if (!$user) {
            $this->json(['verified' => false, 'error' => 'User not found'], 404);
            return;
        }

        // Check if email has been verified
        if ($user['email_verified']) {
            // Clear pending verification
            unset($_SESSION['pending_verification']);
            
            // Set user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            
            $this->json([
                'verified' => true,
                'message' => 'Email verified successfully'
            ]);
        } else {
            $this->json(['verified' => false]);
        }
    }

    public function showResetPassword(): void
    {
        $token = $this->input('token');
        
        if (!$token) {
            $this->redirect('forgot-password');
            return;
        }

        // Verify token exists and hasn't expired
        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            $this->view('auth/reset-password-expired');
            return;
        }

        $this->view('auth/reset-password', [
            'csrf_token' => $this->generateCSRFToken(),
            'token' => $token,
        ]);
    }

    public function resetPassword(): void
    {
        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $token = $this->sanitizeInput($this->input('token'));
        $password = $this->input('password');
        $passwordConfirmation = $this->input('password_confirmation');

        if (empty($token)) {
            $this->json(['error' => 'Invalid reset token'], 400);
            return;
        }

        $errors = $this->validate([
            'password' => 'required|min:8|strong_password',
            'password_confirmation' => 'required',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        if ($password !== $passwordConfirmation) {
            $this->json(['error' => 'Passwords do not match'], 400);
            return;
        }

        // Find user by reset token
        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            $this->json(['error' => 'Invalid or expired reset token'], 400);
            return;
        }

        try {
            // Update password and clear reset token
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $this->userModel->updatePassword($user['id'], $hashedPassword);
            $this->userModel->clearPasswordResetToken($user['id']);

            // Log activity
            $this->logActivity('password_reset_completed', ['user_id' => $user['id']]);

            $this->json([
                'success' => true,
                'message' => 'Password reset successfully! You can now login with your new password.',
            ]);

        } catch (\Exception $e) {
            error_log("Password reset failed: " . $e->getMessage());
            $this->json(['error' => 'Failed to reset password. Please try again.'], 500);
        }
    }
}