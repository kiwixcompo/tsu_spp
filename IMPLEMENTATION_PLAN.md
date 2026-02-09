# Implementation Plan: Staff Type, Profile Visibility & Security

## Overview
This document outlines the implementation of three major features:
1. Profile visibility toggle (public/private) during signup
2. Staff type selection (teaching/non-teaching) with conditional fields
3. Enhanced security measures against malware and attacks

---

## Part 1: Database Changes

### Migration 1: Add staff_type and unit columns
**File:** `database/migrations/005_add_staff_type_and_unit.sql`

```sql
ALTER TABLE profiles 
ADD COLUMN staff_type ENUM('teaching', 'non-teaching') DEFAULT 'teaching' AFTER designation,
ADD COLUMN unit VARCHAR(255) DEFAULT NULL AFTER staff_type;
```

### Migration 2: Create units_offices table
**File:** `database/seeds/units_offices.sql`

Creates table with 51 units/offices/directorates for non-teaching staff.

---

## Part 2: Security Implementation

### Files Created:
1. **config/security.php** - Security configuration
2. **app/Helpers/SecurityHelper.php** - Security functions
3. **storage/logs/security.log** - Security event logging

### Security Features:
- File upload validation (blocks exe, php, scripts, etc.)
- MIME type verification
- Malware detection in uploaded files
- XSS protection
- SQL injection prevention
- CSRF token validation
- Rate limiting
- Security headers
- Input sanitization
- Password strength validation

---

## Part 3: Registration Form Updates

### Changes Needed in `app/Views/auth/register.php`:

1. **Add Staff Type Selection** (before faculty/department)
   - Radio buttons or dropdown
   - Teaching Staff / Non-Teaching Staff
   - Default: Teaching Staff

2. **Add Profile Visibility Toggle**
   - Toggle switch or checkbox
   - Public (visible in directory) / Private (hidden from directory)
   - Default: Public

3. **Conditional Fields Logic**
   - If Teaching Staff selected:
     - Faculty: Required
     - Department: Required
     - Unit: Hidden
   
   - If Non-Teaching Staff selected:
     - Faculty: Optional
     - Department: Optional
     - Unit: Required (dropdown with 51 options)

### JavaScript Logic:
```javascript
// Listen for staff type change
document.getElementById('staff_type').addEventListener('change', function() {
    const staffType = this.value;
    const facultyField = document.getElementById('faculty');
    const departmentField = document.getElementById('department');
    const unitField = document.getElementById('unit');
    
    if (staffType === 'teaching') {
        // Show faculty/department, hide unit
        facultyField.required = true;
        departmentField.required = true;
        unitField.required = false;
        // Show/hide containers
    } else {
        // Show unit, make faculty/department optional
        facultyField.required = false;
        departmentField.required = false;
        unitField.required = true;
        // Show/hide containers
    }
});
```

---

## Part 4: Controller Updates

### AuthController.php - register() method:
```php
// Add validation for new fields
$errors = $this->validate([
    'staff_type' => 'required|in:teaching,non-teaching',
    'profile_visibility' => 'required|in:public,private',
    // Conditional validation
    'faculty' => $staffType === 'teaching' ? 'required' : 'optional',
    'department' => $staffType === 'teaching' ? 'required' : 'optional',
    'unit' => $staffType === 'non-teaching' ? 'required' : 'optional',
]);

// Store in session for profile setup
$_SESSION['registration_data'] = [
    'staff_type' => $staffType,
    'profile_visibility' => $profileVisibility,
    'faculty' => $faculty,
    'department' => $department,
    'unit' => $unit,
];
```

### ProfileController.php - setup() method:
```php
// Add new fields to profileData
$profileData = [
    // ... existing fields ...
    'staff_type' => $registrationData['staff_type'] ?? 'teaching',
    'unit' => $registrationData['unit'] ?? null,
    'profile_visibility' => $registrationData['profile_visibility'] ?? 'public',
];
```

---

## Part 5: Directory Filtering

### DirectoryController.php - index() method:
Update query to exclude private profiles:
```php
$whereConditions = [
    "u.account_status = 'active'",
    "p.profile_visibility = 'public'",  // Only show public profiles
    "u.role != 'admin'"
];
```

---

## Part 6: Profile Display Updates

### Show staff type and unit on profiles:
- Teaching Staff: Show Faculty & Department
- Non-Teaching Staff: Show Unit (and optionally Faculty/Department if provided)

---

## Part 7: Security Integration

### Update FileUploadHelper.php:
```php
use App\Helpers\SecurityHelper;

public static function uploadProfilePhoto($file, $userId) {
    // Validate file security
    $validation = SecurityHelper::validateFileUpload($file, 'photo');
    if (!$validation['valid']) {
        throw new \Exception($validation['error']);
    }
    
    // Sanitize filename
    $filename = SecurityHelper::sanitizeFilename($file['name']);
    
    // ... rest of upload logic ...
}
```

### Update all input handling:
```php
use App\Helpers\SecurityHelper;

// Sanitize all user inputs
$input = SecurityHelper::sanitizeInput($_POST['field_name']);
$email = SecurityHelper::sanitizeEmail($_POST['email']);
```

### Add security headers to public/index.php:
```php
require_once __DIR__ . '/../app/Helpers/SecurityHelper.php';
use App\Helpers\SecurityHelper;

// Set security headers
SecurityHelper::setSecurityHeaders();
```

---

## Part 8: Admin Panel Updates

### Add filters for staff type:
- Filter by Teaching Staff
- Filter by Non-Teaching Staff
- Filter by Unit (for non-teaching)

### Show staff type in user list:
- Add "Staff Type" column
- Add "Unit" column (for non-teaching staff)

---

## Part 9: Testing Checklist

### Registration:
- [ ] Staff type selection works
- [ ] Profile visibility toggle works
- [ ] Teaching staff: Faculty/Department required
- [ ] Non-Teaching staff: Unit required, Faculty/Department optional
- [ ] Form validation works correctly
- [ ] Data saves to database

### Security:
- [ ] Cannot upload .exe, .php, .js files
- [ ] Cannot upload files with PHP code in them
- [ ] File size limits enforced
- [ ] MIME type validation works
- [ ] XSS attempts blocked
- [ ] SQL injection attempts blocked
- [ ] Security headers present
- [ ] Suspicious activity logged

### Directory:
- [ ] Private profiles not shown in directory
- [ ] Public profiles shown in directory
- [ ] Search works correctly
- [ ] Filters work correctly

### Profile Display:
- [ ] Teaching staff shows Faculty/Department
- [ ] Non-teaching staff shows Unit
- [ ] Profile visibility setting respected

---

## Part 10: Deployment Steps

1. **Backup database**
2. **Run migrations:**
   ```sql
   -- Run 005_add_staff_type_and_unit.sql
   -- Run units_offices.sql
   ```

3. **Upload new files:**
   - config/security.php
   - app/Helpers/SecurityHelper.php
   - Updated registration form
   - Updated controllers
   - Updated directory controller

4. **Create logs directory:**
   ```bash
   mkdir -p storage/logs
   chmod 755 storage/logs
   ```

5. **Test thoroughly**

6. **Monitor security.log for suspicious activity**

---

## Security Best Practices Implemented

### File Upload Security:
✅ Extension whitelist (only jpg, png, gif, pdf, doc, docx)
✅ Extension blacklist (blocks exe, php, js, bat, etc.)
✅ MIME type validation
✅ File size limits
✅ Malware detection (checks for PHP/script code)
✅ Double extension prevention
✅ Filename sanitization

### Input Security:
✅ XSS protection (HTML entity encoding)
✅ SQL injection prevention (prepared statements)
✅ CSRF token validation
✅ Input length limits
✅ Special character filtering

### Session Security:
✅ Secure flag (HTTPS only)
✅ HttpOnly flag (no JavaScript access)
✅ SameSite flag (CSRF protection)
✅ Session regeneration
✅ Session timeout

### Headers Security:
✅ X-Frame-Options (clickjacking protection)
✅ X-Content-Type-Options (MIME sniffing protection)
✅ X-XSS-Protection (XSS filter)
✅ Content-Security-Policy (script injection protection)
✅ Referrer-Policy (privacy protection)

### Additional Security:
✅ Rate limiting
✅ IP blocking capability
✅ Security event logging
✅ Password strength validation
✅ Failed login tracking

---

## Files to Create/Modify

### New Files:
1. database/migrations/005_add_staff_type_and_unit.sql
2. database/seeds/units_offices.sql
3. config/security.php
4. app/Helpers/SecurityHelper.php
5. storage/logs/security.log (auto-created)

### Files to Modify:
1. app/Views/auth/register.php
2. app/Controllers/AuthController.php
3. app/Controllers/ProfileController.php
4. app/Controllers/DirectoryController.php
5. app/Helpers/FileUploadHelper.php
6. public/index.php
7. app/Views/directory/index.php
8. app/Views/directory/profile.php
9. app/Views/admin/users.php

---

## Estimated Implementation Time

- Database setup: 30 minutes
- Security implementation: 2 hours
- Registration form updates: 2 hours
- Controller updates: 1 hour
- Directory filtering: 1 hour
- Testing: 2 hours
- **Total: ~8-9 hours**

---

## Support & Maintenance

### Monitoring:
- Check `storage/logs/security.log` regularly
- Monitor failed login attempts
- Review suspicious file upload attempts

### Updates:
- Keep blocked extensions list updated
- Update security headers as needed
- Review and update rate limits

### Backups:
- Regular database backups
- Keep backup of security.log
- Document any security incidents

---

**This implementation provides enterprise-level security while maintaining user-friendly functionality.**
