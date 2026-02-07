# TSU Staff Portal - Deployment Checklist

## ✅ Pre-Deployment Checklist

### 1. Database Migration
- [ ] Backup current database
- [ ] Run SQL: `database/add_staff_number.sql`
- [ ] Verify `staff_number` column exists in `profiles` table
- [ ] Verify unique constraint on `staff_number`

### 2. Storage Folders
- [ ] Create folder: `storage/qrcodes/`
- [ ] Set permissions: `chmod 755 storage/qrcodes`
- [ ] Verify web server can write to folder

### 3. File Upload
Upload these files to production:

**Controllers:**
- `app/Controllers/IDCardController.php`
- `app/Controllers/ProfileController.php`
- `app/Controllers/AuthController.php`
- `app/Controllers/AdminController.php`

**Views:**
- `app/Views/admin/id-card-generator.php`
- `app/Views/admin/id-card-preview.php`
- `app/Views/profile/edit.php`
- `app/Views/auth/register.php`

**Helpers:**
- `app/Helpers/QRCodeHelper.php`

**Routes:**
- `routes/web.php`

**Config:**
- `public/index.php` (environment detection)

## ✅ Post-Deployment Testing

### 1. Registration Flow
- [ ] Go to registration page
- [ ] Verify staff ID dropdown shows TSU/SP/ and TSU/JP/
- [ ] Register a new test user
- [ ] Verify staff number is saved correctly

### 2. Profile Edit
- [ ] Login as existing user
- [ ] Go to Profile Edit
- [ ] Verify name fields (first, middle, last) are present
- [ ] Verify staff number field with dropdown
- [ ] Update profile and save
- [ ] Verify changes are saved

### 3. ID Card Generation (Admin)
- [ ] Login as admin
- [ ] Go to Admin Dashboard > ID Cards
- [ ] Verify user list loads
- [ ] Select a user with profile photo
- [ ] Click "Generate ID Card"
- [ ] Verify preview page loads

### 4. ID Card Preview
- [ ] Verify profile photo appears on front
- [ ] Verify staff number shows correctly
- [ ] Verify name includes middle name (if present)
- [ ] Verify QR code appears on back
- [ ] Verify "Security Unit" text on back
- [ ] Test Print button
- [ ] Test Download PDF button

### 5. QR Code Functionality
- [ ] Scan QR code with phone
- [ ] Verify it opens profile page
- [ ] Check QR code image loads: `/qrcode/{filename}`

### 6. Bulk Generation
- [ ] Select multiple users
- [ ] Click "Generate ID Cards"
- [ ] Verify all cards generate successfully

## ✅ Error Checking

### 1. Error Log
- [ ] Check `error.log` for any errors
- [ ] Verify no PHP fatal errors
- [ ] Verify no database errors

### 2. Browser Console
- [ ] Open browser console (F12)
- [ ] Check for JavaScript errors
- [ ] Verify AJAX requests succeed

### 3. Network Tab
- [ ] Check Network tab in browser
- [ ] Verify all assets load (200 status)
- [ ] Verify QR code images load

## ✅ Common Issues & Solutions

### Issue: QR Code Not Showing
**Solution:**
1. Check `storage/qrcodes/` folder exists
2. Check folder permissions (755)
3. Check error log for QR generation errors
4. Verify external API access (QRServer/QuickChart)

### Issue: Profile Photo Not Showing on ID Card
**Solution:**
1. Check photo path in database
2. Verify photo file exists in `storage/uploads/profiles/`
3. Check file permissions
4. Clear browser cache

### Issue: Staff Number Not Saving
**Solution:**
1. Verify database migration ran successfully
2. Check `staff_number` column exists
3. Check for duplicate staff numbers (unique constraint)
4. Check error log for SQL errors

### Issue: Download PDF Not Working
**Solution:**
1. Check browser console for errors
2. Verify html2canvas and jsPDF libraries load
3. Try Print > Save as PDF instead
4. Check for CORS issues with images

## ✅ Rollback Plan

If deployment fails:

1. **Database Rollback:**
   ```sql
   ALTER TABLE profiles DROP COLUMN staff_number;
   ```

2. **File Rollback:**
   - Restore previous versions from backup
   - Or use Git: `git checkout HEAD~1 <filename>`

3. **Clear Cache:**
   - Clear browser cache
   - Clear server cache if applicable

## ✅ Performance Checks

- [ ] Page load time < 3 seconds
- [ ] ID card generation < 5 seconds
- [ ] QR code generation < 2 seconds
- [ ] Bulk generation works for 10+ users

## ✅ Security Checks

- [ ] Admin routes require admin role
- [ ] CSRF tokens present on all forms
- [ ] File uploads validate file types
- [ ] SQL injection protection (prepared statements)
- [ ] XSS protection (htmlspecialchars)

## ✅ Final Sign-Off

- [ ] All features tested and working
- [ ] No errors in error log
- [ ] Performance acceptable
- [ ] Security verified
- [ ] Documentation updated
- [ ] Stakeholders notified

---

## Quick Test Commands

```bash
# Check folder permissions
ls -la storage/qrcodes/

# Check database column
mysql -u username -p database_name -e "DESCRIBE profiles;"

# Check error log
tail -f error.log

# Test QR code generation
curl https://staff.tsuniversity.edu.ng/public/qrcode/qr_1_1234567890.png
```

## Support Contacts

- **Technical Issues:** Check error.log first
- **Database Issues:** Contact database admin
- **Server Issues:** Contact hosting support

---

**Deployment Date:** _________________

**Deployed By:** _________________

**Sign-Off:** _________________
