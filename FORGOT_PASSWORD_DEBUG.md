# Forgot Password Email Not Sending - Debug Guide

## Issue
The forgot password form shows success message, but:
- Email is not received
- `public/reset_links.txt` doesn't have the link
- `storage/emails/` doesn't have the saved email

## Root Cause Found
The `forgotPassword()` method was returning the success JSON response BEFORE attempting to send the email. This caused the response to be sent immediately, and any email sending errors were happening after the response (silently failing).

## Fix Applied

### Changed Execution Order
**Before:**
```php
// Return success immediately
$this->json(['success' => true, 'message' => '...']);

// Then try to send email (after response sent)
if ($user && $user['email_verified']) {
    // Email sending code...
}
```

**After:**
```php
// Process email sending FIRST
if ($user && $user['email_verified']) {
    // Email sending code with detailed logging...
}

// THEN return success (after processing)
$this->json(['success' => true, 'message' => '...']);
```

### Added Detailed Logging
```php
error_log("Forgot Password: Generating reset token for {$email}");
error_log("Forgot Password: Token saved to database, now sending email");
error_log("Forgot Password: Email send result: " . ($emailResult ? 'SUCCESS' : 'FAILED'));
```

## Debug Steps

### Step 1: Use Debug Script
Access the debug script to diagnose the issue:
```
https://staff.tsuniversity.edu.ng/debug_forgot_password.php
```

This will check:
1. ✅ User exists and email is verified
2. ✅ EmailHelper is working
3. ✅ File permissions are correct
4. ✅ Recent error logs
5. ✅ Test email sending

### Step 2: Check Error Logs
```bash
# View recent errors
tail -f error.log

# Search for password reset errors
grep -i "forgot password\|password reset" error.log
```

### Step 3: Verify User Email is Verified
The most common issue is that the user's email is not verified. Password reset emails are ONLY sent to verified users.

Check in database:
```sql
SELECT id, email, email_verified, account_status 
FROM users 
WHERE email = 'social@tsuniversity.edu.ng';
```

If `email_verified` = 0, the user needs to verify their email first.

### Step 4: Check File Permissions
```bash
# Check if directories are writable
ls -la public/
ls -la storage/emails/

# Make writable if needed
chmod 755 public/
chmod 755 storage/emails/
```

### Step 5: Test Email Sending Directly
Use the debug script's "Send Test Email" button to test email sending directly.

## Common Issues and Solutions

### Issue 1: Email Not Verified
**Symptom:** Success message but no email sent
**Cause:** User's `email_verified` = 0
**Solution:** User must verify email first, or admin can manually verify:
```sql
UPDATE users SET email_verified = 1 WHERE email = 'social@tsuniversity.edu.ng';
```

### Issue 2: Directory Not Writable
**Symptom:** No files created in `public/` or `storage/emails/`
**Cause:** Insufficient permissions
**Solution:**
```bash
chmod 755 public/
chmod 755 storage/emails/
```

### Issue 3: SMTP Configuration
**Symptom:** Email sending fails
**Cause:** SMTP credentials incorrect or server blocking
**Solution:** Check `.env` file:
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=staffprofile@tsuniversity.edu.ng
MAIL_PASSWORD=nfigbwuxcvicbbbd
```

### Issue 4: PHPMailer Not Available
**Symptom:** Emails not sending via SMTP
**Cause:** PHPMailer not installed
**Solution:** System falls back to PHP `mail()` function automatically

## Testing Checklist

- [ ] Access debug script: `/debug_forgot_password.php`
- [ ] Verify user exists and email is verified
- [ ] Check error logs for detailed messages
- [ ] Verify file permissions
- [ ] Click "Send Test Email" button
- [ ] Check `public/reset_links.txt` for link
- [ ] Check `storage/emails/` for saved email
- [ ] Check actual email inbox

## Expected Behavior

When forgot password works correctly:

1. **User submits email**
2. **System checks:**
   - User exists? ✓
   - Email verified? ✓
3. **System generates:**
   - Reset token (64 characters)
   - Expiration time (1 hour)
4. **System saves:**
   - Token to database
   - Link to `public/reset_links.txt`
   - Email HTML to `storage/emails/`
5. **System sends:**
   - Email via SMTP or mail()
6. **System returns:**
   - Success message to user

## Files Modified

1. `app/Controllers/AuthController.php`
   - Fixed execution order (email before response)
   - Added detailed logging
   
2. `debug_forgot_password.php` (NEW)
   - Diagnostic tool to identify issues

## Next Steps

1. **Run debug script:** `/debug_forgot_password.php`
2. **Check error logs:** Look for "Forgot Password:" messages
3. **Verify email status:** Ensure user's email is verified
4. **Test email sending:** Use debug script's test button
5. **Check files:** Look in `public/reset_links.txt` and `storage/emails/`

## Quick Fix for Testing

If you need to test immediately and the user's email is not verified:

```sql
-- Verify the user's email manually
UPDATE users 
SET email_verified = 1, account_status = 'active' 
WHERE email = 'social@tsuniversity.edu.ng';
```

Then try the forgot password again.

## Summary

The main fix was changing the execution order so email sending happens BEFORE the response is sent. This ensures any errors are properly logged and the email is actually sent.

Use the debug script (`/debug_forgot_password.php`) to diagnose the specific issue in your case.
