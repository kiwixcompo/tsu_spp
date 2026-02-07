# Installing PHPMailer (Optional)

## Current Status

The application works WITHOUT PHPMailer. It will use PHP's built-in `mail()` function as a fallback.

However, for better email delivery (especially with Gmail/SMTP), you should install PHPMailer.

## Option 1: Via Composer (Recommended)

If you have SSH access:

```bash
cd /home4/tsuniity/staff.tsuniversity.edu.ng
composer require phpmailer/phpmailer
```

## Option 2: Manual Installation

1. **Download PHPMailer**
   - Go to: https://github.com/PHPMailer/PHPMailer/releases
   - Download the latest release ZIP

2. **Extract and Upload**
   - Extract the ZIP file
   - Upload the `src` folder to: `/home4/tsuniity/staff.tsuniversity.edu.ng/vendor/phpmailer/phpmailer/src/`

3. **Verify Structure**
   ```
   vendor/
   └── phpmailer/
       └── phpmailer/
           └── src/
               ├── PHPMailer.php
               ├── SMTP.php
               └── Exception.php
   ```

## Option 3: Use PHP mail() Function

If you don't need SMTP (Gmail), you can use the server's built-in mail function:

1. No installation needed
2. Emails will be sent using PHP's `mail()` function
3. Works for basic email sending

## Verification

After installation, the error log will no longer show PHPMailer warnings, and emails will be sent via SMTP.

## Note

The application will work fine without PHPMailer. It's only needed if you want to use Gmail SMTP for sending emails.

For now, the application uses PHP's built-in mail() function which should work for most cases.
