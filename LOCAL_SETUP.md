# Local Development Setup

## Quick Start

### 1. Configure Local Environment

The `.env.local` file is already created for you with local settings.

**Edit if needed:**
```
DB_DATABASE=tsu_staff_portal
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Create Local Database

Open phpMyAdmin (http://localhost/phpmyadmin) and:

1. Create database: `tsu_staff_portal`
2. Import: `database/setup_database.sql`
3. Run migration: `database/add_staff_number.sql`

### 3. Access Local Site

**URL:** http://localhost/tsu_spp/public/

### 4. Local vs Production

| Feature | Local (.env.local) | Production (.env) |
|---------|-------------------|-------------------|
| Errors | Displayed on screen | Logged to error.log |
| Debug | Enabled | Disabled |
| Database | localhost/root | Production DB |
| URL | localhost/tsu_spp/public | staff.tsuniversity.edu.ng |
| Email | Logged (not sent) | Sent via SMTP |

## How It Works

### Environment Detection

The application automatically detects which environment to use:

1. **Checks for `.env.local`** (local development)
2. **Falls back to `.env`** (production)

### Local Development Features

✅ **Errors displayed on screen** - Easy debugging
✅ **Debug mode enabled** - Detailed error messages
✅ **Local database** - Safe testing
✅ **Emails logged** - No actual emails sent
✅ **No error logger** - Direct PHP errors shown

### Production Features

✅ **Errors logged to file** - User-friendly error pages
✅ **Debug mode disabled** - Security
✅ **Production database** - Live data
✅ **Emails sent** - Real email delivery
✅ **Custom error logger** - Comprehensive logging

## Testing Workflow

### 1. Test Locally

```
1. Make changes to code
2. Test at: http://localhost/tsu_spp/public/
3. See errors directly on screen
4. Fix any issues
5. Test all features
```

### 2. Deploy to Production

```
1. Double-click: UPDATE.bat
2. Code pushed to GitHub
3. cPanel auto-deploys
4. Test at: https://staff.tsuniversity.edu.ng/public/
```

## Local Database Setup

### Option 1: Import Full Database

```sql
-- In phpMyAdmin, import:
database/setup_database.sql
database/add_staff_number.sql
```

### Option 2: Quick Setup

```sql
-- Create database
CREATE DATABASE tsu_staff_portal;

-- Import tables
-- (Use phpMyAdmin import feature)
```

## Common Local Issues

### Issue: "Database connection failed"

**Solution:** Check `.env.local`:
```
DB_HOST=localhost
DB_DATABASE=tsu_staff_portal
DB_USERNAME=root
DB_PASSWORD=
```

### Issue: "Page not found"

**Solution:** Access via:
```
http://localhost/tsu_spp/public/
```
Not: `http://localhost/tsu_spp/`

### Issue: "Assets not loading"

**Solution:** Check `APP_URL` in `.env.local`:
```
APP_URL=http://localhost/tsu_spp/public
```

## File Structure

```
tsu_spp/
├── .env.local          ← Local config (not in Git)
├── .env.example        ← Template
├── .env                ← Production config (not in Git)
├── public/
│   └── index.php       ← Entry point
├── app/
├── database/
└── ...
```

## Tips

### 1. Keep .env.local

Never commit `.env.local` to Git. It's already in `.gitignore`.

### 2. Test Before Deploy

Always test locally first:
- Register/Login
- Profile creation
- ID card generation
- Admin features

### 3. Use Local Database

Keep local and production databases separate. Test with dummy data locally.

### 4. Check Error Display

In local, errors show on screen. In production, check `error.log`.

## Quick Commands

### Start Local Server (if using PHP built-in)

```bash
cd C:\wamp64\www\tsu_spp\public
php -S localhost:8000
```

Then access: http://localhost:8000/

### Deploy to Production

```bash
# Just double-click:
UPDATE.bat
```

## Environment Files

### .env.local (Local - You)
```
APP_ENV=local
APP_DEBUG=true
DB_HOST=localhost
DB_USERNAME=root
```

### .env (Production - Server)
```
APP_ENV=production
APP_DEBUG=false
DB_HOST=localhost
DB_USERNAME=tsuniity_staff
```

---

**You're all set for local development!**

Access: http://localhost/tsu_spp/public/
