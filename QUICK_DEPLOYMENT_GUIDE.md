# 🚀 Quick Deployment Guide - TSU Staff Portal

## ⚡ Fast Track Deployment (5 Steps)

### Step 1: Upload Files
```bash
# Upload entire project to:
/home/tsuniver/staff.tsuniversity.ng/
```

### Step 2: Run Database Setup
Access: `https://staff.tsuniversity.ng/setup_database_complete.php`
- Click "Start Database Setup"
- Wait for completion
- Note admin credentials

### Step 3: Run Additional Migrations
In phpMyAdmin, run these SQL files:
```sql
SOURCE database/migrations/007_add_id_card_manager_role.sql;
SOURCE database/update_admin_password.sql;
SOURCE database/create_id_card_manager.sql;
```

### Step 4: Test Login
- **Admin:** admin@tsuniversity.ng / Admin123!
- **ID Card Manager:** idcards@tsuniversity.ng / IDCard@2026!

### Step 5: Cleanup
Delete these files from server:
- `setup_database_complete.php`
- `public/setup_database_complete.php`

---

## 🔑 Default Credentials

| Role | Email | Password | Access |
|------|-------|----------|--------|
| Admin | admin@tsuniversity.ng | Admin123! | Full system |
| ID Card Manager | idcards@tsuniversity.ng | IDCard@2026! | ID cards only |

⚠️ **Change passwords immediately after first login!**

---

## 📊 Key URLs

- **Homepage:** https://staff.tsuniversity.ng/public/
- **Login:** https://staff.tsuniversity.ng/public/login
- **Admin Panel:** https://staff.tsuniversity.ng/public/admin/dashboard
- **ID Card Manager:** https://staff.tsuniversity.ng/public/id-card-manager/dashboard
- **Directory:** https://staff.tsuniversity.ng/public/directory

---

## ✅ Quick Test Checklist

- [ ] Homepage loads
- [ ] Can login as admin
- [ ] Can login as ID Card Manager
- [ ] Images/logo display
- [ ] Can register new user
- [ ] Can print ID card
- [ ] Print logs working

---

## 🆘 Troubleshooting

**Issue:** Database connection error  
**Fix:** Check `.env` file has correct credentials

**Issue:** Images not loading  
**Fix:** Check file permissions: `chmod 755 public/uploads/`

**Issue:** Can't access ID Card Manager  
**Fix:** Run migration 007 to add role

**Issue:** 404 errors  
**Fix:** Verify document root points to `/public` folder

---

## 📞 Quick Commands

```bash
# Set permissions
chmod -R 755 storage/
chmod -R 755 public/uploads/
chmod 644 .env

# Check logs
tail -f storage/logs/app.log
tail -f error_log

# Backup database
mysqldump -u username -p database_name > backup.sql
```

---

**For detailed instructions, see:** `DOMAIN_MIGRATION_CHECKLIST.md`  
**For complete summary, see:** `MIGRATION_COMPLETE_SUMMARY.md`
