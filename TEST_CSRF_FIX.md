# Test Script CSRF Token Fix

## Issue
When testing the forgot password functionality using `test_forgot_password.php`, submitting an email address resulted in "Invalid CSRF token" error.

## Root Cause
The test script was not:
1. Starting a PHP session
2. Generating a CSRF token
3. Sending the CSRF token with the form submission

## Solution Applied

### 1. Added Session Initialization
```php
// Start session for CSRF token
session_start();
```

### 2. Added CSRF Token Generation
```php
// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];
```

### 3. Updated JavaScript to Include CSRF Token
```javascript
const csrfToken = '<?= $csrfToken ?>';

// ... in form submission
const formData = new FormData();
formData.append('email', email);
formData.append('csrf_token', csrfToken);  // Added this line
```

## How to Test

1. **Clear your browser cache/cookies** (important!)
2. Go to: `https://staff.tsuniversity.edu.ng/test_forgot_password.php`
3. Scroll to "Test Forgot Password" section
4. Enter a registered email (e.g., `user@tsuniversity.edu.ng`)
5. Click "Send Test Reset Link"
6. ✅ Should now work without CSRF error

## What Changed

**Before:**
- No session started
- No CSRF token generated
- Form submitted without CSRF token
- Result: "Invalid CSRF token" error

**After:**
- Session started on page load
- CSRF token generated and stored in session
- CSRF token included in form submission
- Result: Form submits successfully

## Alternative Testing Method

If you still encounter issues with the test script, you can test the actual forgot password page directly:

1. Go to: `https://staff.tsuniversity.edu.ng/login`
2. Click "Forgot Password?"
3. Enter your email
4. Submit the form
5. Check `public/reset_links.txt` for the reset link

This method uses the application's built-in CSRF handling and is more reliable.

## Files Modified

- `test_forgot_password.php`
  - Added `session_start()`
  - Added CSRF token generation
  - Updated JavaScript to send CSRF token

## Validation

✅ No PHP syntax errors
✅ Session properly initialized
✅ CSRF token properly generated
✅ CSRF token properly sent with form

## Important Notes

1. **Clear Browser Cache:** After the fix, clear your browser cache/cookies to ensure you get a fresh session
2. **Session Cookies:** Make sure your browser accepts cookies from the test domain
3. **HTTPS:** If testing on HTTPS, ensure secure cookies are working

## Troubleshooting

### Still Getting CSRF Error?

1. **Clear browser cache and cookies**
2. **Check browser console** for JavaScript errors
3. **Verify session is working:**
   - Add this to test script: `<?php var_dump($_SESSION); ?>`
   - Should show csrf_token in the output
4. **Check error logs:** `error.log` and `error_log`

### Alternative: Test Without Script

Just use the actual forgot password page:
- URL: `/forgot-password`
- This uses the application's proper session handling
- More reliable than the test script

## Summary

The test script now properly handles CSRF tokens by:
1. Starting a session
2. Generating a CSRF token
3. Sending it with form submissions

The "Invalid CSRF token" error should be resolved. If you still see it, clear your browser cache and try again.
