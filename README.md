# TSU Staff Profile Portal

## 🚀 Quick Start

### Local Development
1. Copy `.env.local` and configure your local database
2. Import database from `database/setup_database.sql`
3. Access: http://localhost/tsu_spp/public/
4. See `LOCAL_SETUP.md` for details

### Deploy to Production
**Double-click:** `UPDATE.bat`

Your changes will be:
1. Committed to Git
2. Pushed to GitHub: https://github.com/kiwixcompo/tsu_spp
3. Auto-deployed to: /home4/tsuniity/staff.tsuniversity.edu.ng/

## 📋 Features

- ✅ Staff Profile Management
- ✅ ID Card Generation with QR Codes
- ✅ Staff Directory
- ✅ Publications Management
- ✅ Admin Dashboard
- ✅ Email Verification
- ✅ Role-based Access Control

## 🔧 Setup

### Database Migration
Run this SQL in phpMyAdmin:
```sql
ALTER TABLE profiles 
ADD COLUMN staff_number VARCHAR(50) DEFAULT NULL AFTER user_id,
ADD UNIQUE KEY unique_staff_number (staff_number);
```

### Create Storage Directory
```bash
mkdir -p storage/qrcodes
chmod 755 storage/qrcodes
```

### Optional: Install PHPMailer
For better email delivery (Gmail SMTP), install PHPMailer:
```bash
composer require phpmailer/phpmailer
```
See `INSTALL_PHPMAILER.md` for details.

**Note:** The application works without PHPMailer using PHP's built-in mail() function.

## 🌐 Links

- **Repository:** https://github.com/kiwixcompo/tsu_spp
- **Production:** https://staff.tsuniversity.edu.ng/public/
- **Deploy Path:** /home4/tsuniity/staff.tsuniversity.edu.ng/

## 📝 Deployment

The `.cpanel.yml` file handles automatic deployment when you push to GitHub.

It will:
- Copy all files to production
- Set correct permissions (PHP: 644, Directories: 755)
- Make storage directories writable
- Clean up unnecessary files

---

**Developed for Taraba State University**
