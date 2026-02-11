# Production Deployment Checklist

## ✅ Updated Configuration

### Database Credentials
```
Database Name: tsuniver_tsu_staff_portal
Username: tsuniver_tsu_staff_portal
Password: fSdohm!4lh.Kk[jD
```

### Application URL
```
https://staff.tsuniversity.ng/public
```

### Deployment Path
```
/home/tsuniver/staff.tsuniversity.ng
```

---

## 📋 Deployment Steps

### 1. Push to Git Repository
```bash
git add .
git commit -m "Production configuration update"
git push origin main
```

### 2. cPanel Will Auto-Deploy
- Files will be deployed to: `/home/tsuniver/staff.tsuniversity.ng/`
- The `.cpanel.yml` handles the deployment automatically

### 3. Upload .env File
Upload `.env.google-workspace` as `.env` to the server:
```
Location: /home/tsuniver/staff.tsuniversity.ng/.env
```

**Via cPanel File Manager:**
1. Navigate to `/home/tsuniver/staff.tsuniversity.ng/`
2. Upload `.env.google-workspace`
3. Rename it to `.env`

### 4. Run Database Setup Script
Access in browser:
```
https://staff.tsuniversity.ng/public/setup_database_complete.php
```

Click "Start Database Setup" and wait for completion.

### 5. Delete Setup Script
After successful setup, delete:
```
/home/tsuniver/staff.tsuniversity.ng/setup_database_complete.php
```

### 6. Set Folder Permissions (if needed)
Via cPanel Terminal or SSH:
```bash
cd /home/tsuniver/staff.tsuniversity.ng
chmod -R 755 storage/
chmod -R 755 public/uploads/
chmod -R 755 storage/qrcodes/
chmod 644 public/.htaccess
```

### 7. Configure Subdomain Document Root
In cPanel → Domains → staff.tsuniversity.ng:
```
Document Root: /home/tsuniver/staff.tsuniversity.ng/public
```

---

## 🔐 Default Admin Credentials

After database setup, use these credentials to login:

```
Email: admin@tsuniversity.ng
Password: Admin@2026!
Login URL: https://staff.tsuniversity.ng/public/login
```

**⚠️ IMPORTANT:** Change the admin password immediately after first login!

---

## ✅ Verification Checklist

After deployment, verify:

- [ ] Website loads at https://staff.tsuniversity.ng/public
- [ ] Login page displays correctly
- [ ] Can login with admin credentials
- [ ] Registration works
- [ ] Email verification works
- [ ] Profile photos upload correctly
- [ ] ID card generation works
- [ ] QR codes display
- [ ] Admin panel accessible
- [ ] All links work correctly

---

## 🔧 Troubleshooting

### Issue: 404 Not Found
**Solution:** Check subdomain document root points to `/public` folder

### Issue: Database Connection Error
**Solution:** Verify `.env` file has correct database credentials

### Issue: Emails Not Sending
**Solution:** 
1. Check MAIL_* settings in `.env`
2. Verify Google App Password is correct
3. Test with: https://staff.tsuniversity.ng/public/test-email.php (if created)

### Issue: Profile Photos Not Displaying
**Solution:**
```bash
chmod -R 755 public/uploads/
```

### Issue: 500 Internal Server Error
**Solution:**
1. Check `.htaccess` exists in `public/` folder
2. Check PHP version (requires 7.4+)
3. Check error logs in cPanel

---

## 📁 Important Files

### Environment Files (Choose One)
- `.env.google-workspace` - **RECOMMENDED** (Has Google SMTP configured)
- `.env.production` - Alternative with placeholders
- `.env.production.FINAL` - Alternative configuration

### Upload as `.env` on Server
```
/home/tsuniver/staff.tsuniversity.ng/.env
```

### Database Setup Script
```
/home/tsuniver/staff.tsuniversity.ng/setup_database_complete.php
```
**⚠️ DELETE AFTER USE!**

---

## 🚀 Quick Start Commands

### Via SSH/Terminal:
```bash
# Navigate to project
cd /home/tsuniver/staff.tsuniversity.ng

# Set permissions
chmod -R 755 storage/ public/uploads/ storage/qrcodes/
chmod 644 public/.htaccess

# View logs (if issues)
tail -f storage/logs/error.log
```

---

## 📧 Email Configuration

### Current Setup (Google Workspace)
```
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@tsuniversity.ng
MAIL_PASSWORD=ywfsmktkxmyxidjn
```

### If Emails Fail
Try alternative port:
```
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

---

## 🔒 Security Notes

1. **Delete setup script** after database initialization
2. **Change admin password** immediately after first login
3. **Keep .env file secure** - never commit to Git
4. **Regular backups** of database and uploads folder
5. **Monitor error logs** regularly

---

## 📞 Support

If you encounter issues:
1. Check cPanel error logs
2. Check application logs: `storage/logs/error.log`
3. Verify all environment variables in `.env`
4. Ensure folder permissions are correct (755 for folders, 644 for files)

---

## ✨ Deployment Complete!

Your TSU Staff Portal is now configured for production at:
**https://staff.tsuniversity.ng/public**

Remember to:
- ✅ Delete `setup_database_complete.php` after use
- ✅ Change admin password
- ✅ Test all functionality
- ✅ Monitor for any errors

Good luck! 🎉
