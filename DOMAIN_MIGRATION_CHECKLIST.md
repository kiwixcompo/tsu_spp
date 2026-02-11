# 🚀 Complete Domain Migration Checklist (.edu.ng → .ng)

This checklist covers ALL steps needed to migrate a PHP application from `.edu.ng` to `.ng` domain.

---

## ✅ PHASE 1: PRE-MIGRATION PREPARATION

### 1.1 Backup Everything
- [ ] Export complete database via phpMyAdmin
- [ ] Download all application files via FTP/cPanel File Manager
- [ ] Save current `.env` files
- [ ] Document current database credentials
- [ ] Take screenshots of working features

### 1.2 Document Current Configuration
- [ ] Note current domain: `staff.tsuniversity.edu.ng`
- [ ] Note current deployment path: `/home/username/old_path`
- [ ] Note database name, username, password
- [ ] Note email configuration (SMTP settings)
- [ ] List all external API integrations

---

## ✅ PHASE 2: CPANEL DEPLOYMENT CONFIGURATION

### 2.1 Update .cpanel.yml
**File:** `.cpanel.yml`

```yaml
---
deployment:
  tasks:
    - export DEPLOYPATH=/home/tsuniver/staff.tsuniversity.ng/
    - /bin/cp -R * $DEPLOYPATH
    - find $DEPLOYPATH -name "*.php" -exec chmod 644 {} \;
    - find $DEPLOYPATH -type d -exec chmod 755 {} \;
    - chmod -R 755 $DEPLOYPATH/storage/ 2>/dev/null || true
    - chmod -R 755 $DEPLOYPATH/public/uploads/ 2>/dev/null || true
    - chmod -R 755 $DEPLOYPATH/storage/qrcodes/ 2>/dev/null || true
    - rm -f $DEPLOYPATH/.git* 2>/dev/null || true
    - rm -f $DEPLOYPATH/*.bat 2>/dev/null || true
    - rm -f $DEPLOYPATH/.cpanel.yml 2>/dev/null || true
```

**Changes:**
- Update `DEPLOYPATH` to new domain folder
- Ensure path matches: `/home/username/staff.tsuniversity.ng/`

---

## ✅ PHASE 3: ENVIRONMENT FILES

### 3.1 Update All .env Files

**Files to update:**
- `.env`
- `.env.production`
- `.env.google-workspace`
- `.env.local`
- `.env.example`

**Required Changes:**

```env
# Application Settings
APP_NAME="TSU Staff Portal"
APP_ENV=production
APP_URL=https://staff.tsuniversity.ng/public  # ← UPDATE THIS
APP_DEBUG=false

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tsuniver_tsu_staff_portal  # ← UPDATE THIS
DB_USERNAME=tsuniver_tsu_staff_portal  # ← UPDATE THIS
DB_PASSWORD=your_new_password  # ← UPDATE THIS

# Email Configuration
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@tsuniversity.ng  # ← UPDATE THIS
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tsuniversity.ng  # ← UPDATE THIS
MAIL_FROM_NAME="TSU Staff Portal"
```

---

## ✅ PHASE 4: APPLICATION CONFIGURATION FILES

### 4.1 Update config/app.php

**File:** `config/app.php`

**Find and replace:**
```php
// OLD
'url' => ($_SERVER['HTTP_HOST'] ?? '') === 'staff.tsuniversity.edu.ng' ? 'https://staff.tsuniversity.edu.ng' : (...),

// NEW
'url' => ($_SERVER['HTTP_HOST'] ?? '') === 'staff.tsuniversity.ng' ? 'https://staff.tsuniversity.ng/public' : ($_ENV['APP_URL'] ?? 'http://localhost/public'),
```

**Also update:**
```php
'env' => ($_SERVER['HTTP_HOST'] ?? '') === 'staff.tsuniversity.ng' ? 'production' : ($_ENV['APP_ENV'] ?? 'local'),
'debug' => ($_SERVER['HTTP_HOST'] ?? '') !== 'staff.tsuniversity.ng' && filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
```

### 4.2 Update config/mail.php

**File:** `config/mail.php`

```php
'from' => [
    'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@tsuniversity.ng',  // ← UPDATE
    'name' => $_ENV['MAIL_FROM_NAME'] ?? 'TSU Staff Portal',
],
```

---

## ✅ PHASE 5: URL HELPER UPDATES

### 5.1 Update app/Helpers/UrlHelper.php

**File:** `app/Helpers/UrlHelper.php`

**Replace entire `getBaseUrl()` function:**

```php
function getBaseUrl(): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Production server - staff.tsuniversity.ng
    if ($host === 'staff.tsuniversity.ng') {
        return 'https://staff.tsuniversity.ng/public';
    }
    
    // Local development
    return $protocol . '://' . $host . '/public';
}
```

**Key Changes:**
- Remove old domain check (`staff.tsuniversity.edu.ng`)
- Remove old path references (`/tsu_spp/public/`)
- Use clean paths: `/public`

---

## ✅ PHASE 6: VIEW FILES - REMOVE HARDCODED PATHS

### 6.1 Update Error Pages

**File:** `app/Views/errors/404.php`

```php
// OLD
<a href="/tsu_spp/public/" class="btn btn-primary">

// NEW
<a href="<?= url() ?>" class="btn btn-primary">
```

**File:** `app/Views/errors/500.php`

```php
// OLD
<a href="/tsu_spp/public/" class="btn btn-primary">

// NEW
<a href="<?= url() ?>" class="btn btn-primary">
```

### 6.2 Search for Hardcoded Paths

**Run this search in your IDE:**
- Search for: `tsu_spp`
- Search for: `.edu.ng`
- Search for: hardcoded `/public/` paths

**Replace all with:**
- Use `url()` helper function
- Use `asset()` for images/CSS/JS
- Use `.ng` domain

---

## ✅ PHASE 7: CONTROLLER UPDATES

### 7.1 Update app/Core/Controller.php

**File:** `app/Core/Controller.php`

**Find the `redirect()` method:**

```php
// OLD
if (!preg_match('/^https?:\/\//', $url) && strpos($url, '/tsu_spp/public/') === false) {

// NEW
if (!preg_match('/^https?:\/\//', $url)) {
```

**Remove all references to old paths in redirect logic**

---

## ✅ PHASE 8: DATABASE SETUP

### 8.1 Get New Database Credentials from cPanel

- [ ] Login to cPanel
- [ ] Go to MySQL Databases
- [ ] Note database name (e.g., `tsuniver_tsu_staff_portal`)
- [ ] Note username (e.g., `tsuniver_tsu_staff_portal`)
- [ ] Note/create password

### 8.2 Update Database Setup Scripts

**Files:**
- `setup_database_complete.php`
- `public/setup_database_complete.php`

**Update default credentials:**

```php
// Database configuration - PRODUCTION DEFAULTS
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_DATABASE'] ?? 'tsuniver_tsu_staff_portal';  // ← UPDATE
$username = $_ENV['DB_USERNAME'] ?? 'tsuniver_tsu_staff_portal';  // ← UPDATE
$password = $_ENV['DB_PASSWORD'] ?? 'your_password';  // ← UPDATE
```

### 8.3 Run Database Setup

- [ ] Upload setup script to server
- [ ] Access: `https://staff.tsuniversity.ng/setup_database_complete.php`
- [ ] Verify all tables created
- [ ] Note admin credentials
- [ ] **DELETE setup script after success**

---

## ✅ PHASE 9: ASSET PATHS & UPLOADS

### 9.1 Verify Asset Paths

**Check these are using helpers:**
```php
// Images
<img src="<?= asset('assets/images/tsu-logo.png') ?>" />

// CSS/JS
<link href="<?= asset('assets/css/style.css') ?>" />

// Uploads
<img src="<?= url('public/uploads/profiles/' . $filename) ?>" />
```

### 9.2 Update Upload Paths in Controllers

**Ensure uploads go to:**
- `public/uploads/profiles/` (NOT `storage/uploads/`)
- `public/uploads/documents/`
- `storage/qrcodes/` (for QR codes)

---

## ✅ PHASE 10: DEPLOYMENT

### 10.1 Upload Files to Server

**Via cPanel File Manager or FTP:**
- [ ] Upload all files to `/home/username/staff.tsuniversity.ng/`
- [ ] Ensure `.env` file is uploaded
- [ ] Verify `.htaccess` files are present
- [ ] Check file permissions (755 for directories, 644 for files)

### 10.2 Set Permissions

```bash
# Directories
chmod 755 storage/
chmod 755 storage/logs/
chmod 755 storage/qrcodes/
chmod 755 public/uploads/
chmod 755 public/uploads/profiles/

# Files
chmod 644 .env
chmod 644 index.php
chmod 644 public/index.php
```

### 10.3 Configure Document Root

**In cPanel → Domains:**
- [ ] Point domain to: `/home/username/staff.tsuniversity.ng/public`
- [ ] OR ensure `.htaccess` redirects properly

---

## ✅ PHASE 11: DNS & SSL

### 11.1 DNS Configuration

- [ ] Update A record to point to new server IP
- [ ] Wait for DNS propagation (up to 48 hours)
- [ ] Verify with: `nslookup staff.tsuniversity.ng`

### 11.2 SSL Certificate

- [ ] Install SSL via cPanel (Let's Encrypt)
- [ ] Force HTTPS in `.htaccess`
- [ ] Verify SSL is working

---

## ✅ PHASE 12: TESTING

### 12.1 Basic Functionality Tests

- [ ] Homepage loads correctly
- [ ] Logo and images display
- [ ] Registration works
- [ ] Login works
- [ ] Email verification works
- [ ] Password reset works
- [ ] Profile creation works
- [ ] Profile editing works
- [ ] File uploads work
- [ ] ID card generation works
- [ ] Admin panel accessible
- [ ] Directory/search works

### 12.2 URL Tests

- [ ] All internal links work
- [ ] No 404 errors
- [ ] No mixed content warnings (HTTP/HTTPS)
- [ ] Asset paths resolve correctly
- [ ] Upload paths work

### 12.3 Email Tests

- [ ] Registration emails send
- [ ] Verification emails send
- [ ] Password reset emails send
- [ ] Emails have correct domain in links

---

## ✅ PHASE 13: POST-MIGRATION CLEANUP

### 13.1 Delete Unnecessary Files

**Delete these files:**
- [ ] `setup_database_complete.php` (root)
- [ ] `public/setup_database_complete.php`
- [ ] `database/update_admin_password.php`
- [ ] Any `*_old.php` or `*_backup.php` files
- [ ] Test/debug files
- [ ] `.cpanel.yml` (optional, for security)

### 13.2 Delete Duplicate/Old Files

- [ ] Old controller versions (`*Controller2.php`, `*_updated.php`)
- [ ] Old view versions (`*2.php`, `*3.php`, `*4.php`)
- [ ] Old helper versions
- [ ] Backup `.env` files (`.env.production.FINAL`, etc.)

### 13.3 Security Hardening

- [ ] Change default admin password
- [ ] Remove debug/test accounts
- [ ] Disable `APP_DEBUG` in production
- [ ] Review file permissions
- [ ] Check `.gitignore` excludes sensitive files

---

## ✅ PHASE 14: MONITORING & VERIFICATION

### 14.1 Error Monitoring

- [ ] Check `error_log` files
- [ ] Monitor `storage/logs/` directory
- [ ] Set up error notifications

### 14.2 Performance Check

- [ ] Test page load speeds
- [ ] Verify database queries are optimized
- [ ] Check image optimization

### 14.3 User Acceptance Testing

- [ ] Have admin test all features
- [ ] Have regular users test registration/login
- [ ] Test ID card printing workflow
- [ ] Verify email delivery

---

## ✅ PHASE 15: DOCUMENTATION

### 15.1 Update Documentation

- [ ] Update README with new domain
- [ ] Document new database credentials (securely)
- [ ] Update deployment instructions
- [ ] Document admin credentials

### 15.2 Create Backup Schedule

- [ ] Set up automated database backups
- [ ] Set up file backups
- [ ] Document restore procedures

---

## 🎯 QUICK REFERENCE: FILES TO UPDATE

### Configuration Files (8 files)
1. `.cpanel.yml` - Deployment path
2. `.env` - All environment variables
3. `.env.production` - Production settings
4. `.env.google-workspace` - Email settings
5. `.env.local` - Local development
6. `.env.example` - Example template
7. `config/app.php` - App URL and domain checks
8. `config/mail.php` - Email from address

### Helper Files (1 file)
9. `app/Helpers/UrlHelper.php` - Base URL generation

### Core Files (1 file)
10. `app/Core/Controller.php` - Redirect method

### View Files (2+ files)
11. `app/Views/errors/404.php` - Error page links
12. `app/Views/errors/500.php` - Error page links
13. Any views with hardcoded paths (search for `tsu_spp` or `.edu.ng`)

### Database Files (2 files)
14. `setup_database_complete.php` - Root setup script
15. `public/setup_database_complete.php` - Public setup script

---

## 🔍 SEARCH & REPLACE CHECKLIST

Run these searches across your entire codebase:

1. **Search:** `staff.tsuniversity.edu.ng`
   - **Replace:** `staff.tsuniversity.ng`

2. **Search:** `tsu_spp/public`
   - **Replace:** `public` or use `url()` helper

3. **Search:** `/tsu_spp/`
   - **Replace:** `/` or use `url()` helper

4. **Search:** `@tsuniversity.edu.ng`
   - **Replace:** `@tsuniversity.ng`

5. **Search:** `storage/uploads/profiles`
   - **Replace:** `public/uploads/profiles`

---

## ⚠️ COMMON PITFALLS TO AVOID

1. **Forgetting to update .env files** - Most common issue
2. **Not updating UrlHelper.php** - Causes broken asset paths
3. **Hardcoded paths in views** - Search for all occurrences
4. **Wrong document root** - Must point to `/public` folder
5. **File permissions** - 755 for dirs, 644 for files
6. **Not deleting setup scripts** - Security risk
7. **Mixed HTTP/HTTPS** - Force HTTPS everywhere
8. **Email domain mismatch** - Update MAIL_FROM_ADDRESS
9. **Database credentials** - Must match cPanel settings
10. **Not testing thoroughly** - Test every feature

---

## 📋 FINAL VERIFICATION CHECKLIST

Before going live:

- [ ] All URLs use new domain
- [ ] No hardcoded old paths remain
- [ ] Database connected successfully
- [ ] Emails sending with correct domain
- [ ] SSL certificate installed and working
- [ ] All assets (images, CSS, JS) loading
- [ ] File uploads working
- [ ] Admin can login
- [ ] Users can register
- [ ] ID cards generate correctly
- [ ] No errors in error_log
- [ ] Setup scripts deleted
- [ ] Backups created
- [ ] Documentation updated

---

## 🎉 MIGRATION COMPLETE!

Your application is now successfully migrated to the new domain.

**Next Steps:**
1. Monitor for 24-48 hours
2. Address any user-reported issues
3. Update any external links/bookmarks
4. Notify users of new domain
5. Set up redirect from old domain (if still active)

---

**Created:** February 11, 2026  
**Version:** 1.0  
**For:** TSU Staff Portal Migration (.edu.ng → .ng)
