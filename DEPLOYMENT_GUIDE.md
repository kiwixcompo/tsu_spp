# 🚀 TSU Staff Profile - Deployment Guide

## 🎯 Quick Start (First Time)

### Step 1: Run Setup (One Time Only)
Double-click: **`deploy-all.bat`**

This will:
- Initialize Git repository
- Set up your Git credentials
- Add GitHub remote
- Create .gitignore
- Commit and push your code

### Step 2: Future Deployments
Just double-click: **`deploy-all.bat`** or **`deploy.bat`**

## 📁 Deployment Scripts

### `deploy-all.bat` ⭐ RECOMMENDED
- **All-in-one solution**
- Checks if Git is set up
- Initializes Git if needed
- Commits and pushes code
- Works for first time and subsequent deployments

### `setup-git.bat`
- Only for Git initialization
- Run once if you want to set up Git separately

### `deploy.bat`
- For deployments after Git is set up
- Requires Git to be initialized first

## Quick Deploy to GitHub & cPanel

### Option 1: All-in-One (Recommended)
Simply double-click **`deploy-all.bat`** file

### Option 2: Linux/Mac (Terminal)
```bash
chmod +x deploy.sh
./deploy.sh
```

### Option 3: Manual Git Commands
```bash
git add .
git commit -m "Your commit message"
git push origin main
```

## What Happens When You Deploy

1. **Local → GitHub**
   - Your code is pushed to: https://github.com/kiwixcompo/TSU_Staff_Profile
   
2. **GitHub → cPanel (Automatic)**
   - cPanel detects the push
   - Reads `.cpanel.yml` configuration
   - Deploys to: `/home4/tsuniity/staff.tsuniversity.edu.ng/`
   - Sets correct permissions automatically

## Deployment Configuration

The `.cpanel.yml` file handles:
- ✅ Copying all files to production
- ✅ Setting PHP files to 644 permissions
- ✅ Setting directories to 755 permissions
- ✅ Making storage directories writable
- ✅ Making uploads directories writable
- ✅ Making QR code directory writable
- ✅ Cleaning up Git files
- ✅ Removing deployment files

## First Time Setup

### 1. Connect Git to GitHub
```bash
git remote add origin https://github.com/kiwixcompo/TSU_Staff_Profile.git
```

### 2. Set Your Git Identity (if not done)
```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

### 3. Authenticate with GitHub
You may need to:
- Use GitHub Personal Access Token
- Or set up SSH keys
- Or use GitHub Desktop

## After Deployment

### Check Deployment Status
1. Log into cPanel
2. Go to "Git Version Control"
3. Check deployment logs

### Verify on Server
Visit: https://staff.tsuniversity.edu.ng/public/

### Run Database Migrations (if needed)
Via phpMyAdmin, run:
```sql
ALTER TABLE profiles 
ADD COLUMN staff_number VARCHAR(50) DEFAULT NULL AFTER user_id,
ADD UNIQUE KEY unique_staff_number (staff_number);
```

### Create Required Directories (if needed)
Via SSH or File Manager:
```bash
mkdir -p storage/qrcodes
chmod 755 storage/qrcodes
```

## Troubleshooting

### "Permission denied" error
```bash
chmod +x deploy.sh
```

### "Git not found" error
Install Git:
- Windows: https://git-scm.com/download/win
- Mac: `brew install git`
- Linux: `sudo apt-get install git`

### "Authentication failed" error
Use GitHub Personal Access Token:
1. Go to GitHub Settings → Developer settings → Personal access tokens
2. Generate new token with 'repo' permissions
3. Use token as password when pushing

### "Branch 'main' not found" error
Your branch might be named 'master':
```bash
git push origin master
```

## Files Included

- `.cpanel.yml` - cPanel deployment configuration
- `deploy.bat` - Windows deployment script
- `deploy.sh` - Linux/Mac deployment script
- `DEPLOYMENT_GUIDE.md` - This file

## Important Notes

⚠️ **Before First Deploy:**
- Ensure `.env` file is in `.gitignore` (it is)
- Check that sensitive data is not committed
- Verify GitHub repository is set up

⚠️ **After Deploy:**
- Check file permissions on server
- Verify database migrations ran
- Test the application
- Check error logs if issues occur

## Support

If deployment fails:
1. Check cPanel Git deployment logs
2. Check server error logs at `/home4/tsuniity/staff.tsuniversity.edu.ng/error_log`
3. Verify file permissions
4. Ensure database is up to date

## Quick Reference

**Repository:** https://github.com/kiwixcompo/TSU_Staff_Profile
**Production:** https://staff.tsuniversity.edu.ng/public/
**Deploy Path:** /home4/tsuniity/staff.tsuniversity.edu.ng/

---

**Happy Deploying! 🎉**
