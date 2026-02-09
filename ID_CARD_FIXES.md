# ID Card Fixes - Completed

## Issues Fixed

### 1. Middle Name Not Displayed on ID Card ✅
**Problem**: When a person has 3 names (first, middle, last), only first and last names were shown on the ID card.

**Solution**: Updated the name display logic to include middle name.

**Files Modified**:
- `app/Views/admin/id-card-preview.php`
  - Updated full name display to include middle name
  - Updated page title to include middle name
  - Updated PDF filename to include middle name

**Before**:
```php
$fullName = ($profile['title'] ?? '') . ' ' . ($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '');
```

**After**:
```php
$nameParts = array_filter([
    $profile['title'] ?? '',
    $profile['first_name'] ?? '',
    $profile['middle_name'] ?? '',
    $profile['last_name'] ?? ''
]);
$fullName = implode(' ', $nameParts);
```

**Result**: 
- Prof. John Michael Smith (all 3 names displayed)
- Dr. Mary Jane Doe (all 3 names displayed)
- Mr. David Lee (2 names work fine too)

### 2. Blood Group Not Visible on ID Card ✅
**Problem**: Blood group field was not being fetched from database, so it couldn't be displayed on the ID card back even when users had added it.

**Solution**: Added `blood_group` field to all SQL queries in IDCardController.

**Files Modified**:
- `app/Controllers/IDCardController.php`
  - Added `p.blood_group` to all SELECT queries (5 locations)
  - Added `blood_group` to HTML entity decoding array (3 locations)

**Queries Updated**:
1. `index()` method - Get all users with profiles
2. `generate()` method - Generate ID card for specific user
3. `preview()` method - Show ID card preview
4. `bulkGenerate()` method - Bulk generate ID cards

**Before**:
```sql
SELECT u.id, u.email, 
       p.id as profile_id, p.title, p.first_name, p.middle_name, p.last_name,
       p.designation, p.faculty, p.department, p.profile_photo, 
       p.profile_slug, p.qr_code_path, p.staff_number
FROM users u
INNER JOIN profiles p ON u.id = p.user_id
```

**After**:
```sql
SELECT u.id, u.email, 
       p.id as profile_id, p.title, p.first_name, p.middle_name, p.last_name,
       p.designation, p.faculty, p.department, p.profile_photo, 
       p.profile_slug, p.qr_code_path, p.staff_number, p.blood_group
FROM users u
INNER JOIN profiles p ON u.id = p.user_id
```

**Result**: 
- Blood group now displays correctly on ID card back
- Shows actual blood type (A+, B-, O+, etc.) when available
- Shows "Not Added" in gray when not set

## Testing Checklist

- [x] ID card displays all 3 names when present (Title + First + Middle + Last)
- [x] ID card displays 2 names correctly when middle name is empty
- [x] Blood group displays on ID card back when set
- [x] Blood group shows "Not Added" when not set
- [x] PDF filename includes all names
- [x] Page title includes all names
- [x] All SQL queries include blood_group field
- [x] HTML entity decoding includes blood_group

## Display Examples

### Full Name Display
- **3 Names**: Prof. John Michael Smith
- **2 Names**: Dr. Mary Johnson
- **With Title**: Mr. David Lee
- **Without Title**: Sarah Williams

### Blood Group Display
- **Set**: A+ (displayed in red)
- **Not Set**: Not Added (displayed in gray)

## Notes

- Middle name is optional - if empty, it's simply skipped
- Blood group is always displayed on ID card back (either value or "Not Added")
- All name parts are properly HTML-encoded for security
- PDF filenames handle empty middle names gracefully
