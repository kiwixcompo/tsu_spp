# Domain Migration: .edu.ng → .ng - COMPLETED

## Migration Summary
**Old Domain:** staff.tsuniversity.edu.ng  
**New Domain:** staff.tsuniversity.ng  
**Deployment Path:** /home/tsuniver/public_html/staff.tsuniversity.ng

---

## Files Updated

### 1. Deployment Configuration
- ✅ `.cpanel.yml` - Updated deployment path to `/home/tsuniver/public_html/staff.tsuniversity.ng`

### 2. Environment Files
- ✅ `.env.example` - Updated APP_URL and email addresses
- ✅ `.env.production` - Updated APP_URL and deployment path
- ✅ `.env.production.FINAL` - Updated APP_URL and email addresses
- ✅ `.env.google-workspace` - Updated APP_URL and email addresses

### 3. Configuration Files
- ✅ `config/app.php` - Updated domain detection logic
- ✅ `config/mail.php` - Updated default email address

### 4. Other Files
- ✅ `sync_faculties.php` - Updated access URL comment

---

## Deployment Steps

### Step 1: Update .cpanel.yml (DONE)
The deployment path is now:
```yaml
export DEPLOYPATH=/home/tsuniver/public_html/staff.tsuniversity.ng
```

### Step 2: Create Subdomain in cPanel
1. Log into cPanel
2. Go to "Domains" or "Subdomains"
3. Create subdomain: `staff.tsuniversity.ng`
4. Document root should be: `/home/tsuniver/public_html/staff.tsuniversity.ng/public`

### Step 3: Deploy via Git
1. Push changes to your Git repository
2. cPanel will automatically deploy to the new path
3. Files will be copied to `/home/tsuniver/public_html/staff.tsuniversity.ng/`

### Step 4: Upload .env File
Upload one of these files as `.env` to the root directory:
- `.env.production` (recommended)
- `.env.google-workspace` (if using Google Workspace email)

**Location:** `/home/tsuniver/public_html/staff.tsuniversity.ng/.env`

### Step 5: Set Folder Permissions
Run these commands via SSH or cPanel Terminal:
```bash
cd /home/tsuniver/public_html/staff.tsuniversity.ng
chmod -R 755 storage/
chmod -R 755 public/uploads/
chmod -R 755 storage/qrcodes/
chmod 644 public/.htaccess
```

### Step 6: Update Database Connection
Edit the `.env` file on the server and update:
```env
DB_HOST=localhost
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### Step 7: Point Subdomain to Public Folder
In cPanel, ensure the subdomain document root points to:
```
/home/tsuniver/public_html/staff.tsuniversity.ng/public
```

---

## Email Configuration

### Option 1: Google Workspace (Recommended)
Use `.env.google-workspace` with these settings:
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@tsuniversity.ng
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@tsuniversity.ng
```

### Option 2: cPanel Email
Update `.env` with cPanel mail settings:
```env
MAIL_HOST=mail.tsuniversity.ng
MAIL_PORT=587
MAIL_USERNAME=noreply@tsuniversity.ng
MAIL_PASSWORD=your-email-password
MAIL_FROM_ADDRESS=noreply@tsuniversity.ng
```

---

## Domain Changes Summary

### URLs Changed
| Old | New |
|-----|-----|
| staff.tsuniversity.edu.ng | staff.tsuniversity.ng |
| staffportal.tsuniversity.edu.ng | staff.tsuniversity.ng |

### Email Addresses Changed
| Old | New |
|-----|-----|
| noreply@tsuniversity.edu.ng | noreply@tsuniversity.ng |
| *@tsuniversity.edu.ng | *@tsuniversity.ng |

---

## Verification Checklist

After deployment, verify:

- [ ] Website loads at https://staff.tsuniversity.ng
- [ ] Login page works
- [ ] Registration works
- [ ] Email verification works
- [ ] Profile photos display correctly
- [ ] ID card generation works
- [ ] QR codes display correctly
- [ ] Admin panel accessible
- [ ] Database connection working
- [ ] File uploads working (storage/uploads/)

---

## Troubleshooting

### Issue: Website shows 404
**Solution:** Check subdomain document root points to `/public` folder

### Issue: Database connection error
**Solution:** Update `.env` file with correct database credentials

### Issue: Emails not sending
**Solution:** 
1. Check MAIL_* settings in `.env`
2. Verify email account exists in cPanel
3. Test with Google Workspace SMTP if cPanel mail fails

### Issue: Profile photos not displaying
**Solution:** 
1. Check folder permissions: `chmod -R 755 public/uploads/`
2. Verify photos are in `public/uploads/profiles/`

### Issue: 500 Internal Server Error
**Solution:**
1. Check `.htaccess` file exists in `public/` folder
2. Check PHP version (requires PHP 7.4+)
3. Check error logs in cPanel

---

## Important Notes

1. **Email Domain:** User emails will still use @tsuniversity.edu.ng format during registration
2. **System Emails:** System emails (noreply) now use @tsuniversity.ng
3. **Old Domain:** The old domain (.edu.ng) will no longer work after DNS changes
4. **Database:** No database changes needed - same database can be used
5. **Existing Users:** Existing users can continue logging in with their credentials

---

## Post-Deployment Tasks

1. Test all functionality thoroughly
2. Update any external links pointing to old domain
3. Set up SSL certificate for new domain (Let's Encrypt in cPanel)
4. Update Google Workspace settings if using Gmail SMTP
5. Monitor error logs for any issues
6. Backup database before making any changes

---

## Support

If you encounter issues:
1. Check cPanel error logs
2. Check application logs in `storage/logs/`
3. Verify all environment variables in `.env`
4. Ensure folder permissions are correct
5. Test database connection separately

---

## Files That DON'T Need Changes

These files reference the domain but don't need updates:
- `database/simple_setup.sql` - Sample data only
- `database/setup_database.sql` - Sample data only
- `storage/emails/*.html` - Old email logs
- `public/verification_codes.txt` - Old verification logs
- `STAFF_NUMBER_ADDITION.md` - Documentation only

---

## Deployment Complete! 🎉

Your application is now configured for the new domain: **staff.tsuniversity.ng**
