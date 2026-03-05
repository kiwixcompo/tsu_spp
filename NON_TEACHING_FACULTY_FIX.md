# Non-Teaching Staff Faculty Registration Fix

## Issue
Non-teaching staff who work at the faculty level (without being assigned to a specific department) were unable to register. The system was requiring both faculty AND department to be selected.

## Problem
The validation logic was:
```
If faculty is selected → department MUST also be selected
```

This prevented faculty-level non-teaching staff (like faculty administrators, secretaries, etc.) from registering.

## Solution
Changed the validation to allow non-teaching staff to select:
- **Option 1:** Unit/Office only
- **Option 2:** Faculty only (department optional)
- **Option 3:** Faculty + Department

## Changes Made

### 1. Backend Validation (`app/Controllers/AuthController.php`)

**Before:**
```php
// Check if at least one option is selected
if (empty($unit) && empty($faculty) && empty($department)) {
    $errors['staff_location'] = 'Please select either a Unit/Office OR Faculty/Department';
}

// If faculty is selected, department must also be selected
if (!empty($faculty) && empty($department)) {
    $errors['department'] = 'Please select a department for the selected faculty';
}
```

**After:**
```php
// Check if at least one option is selected (unit OR faculty)
if (empty($unit) && empty($faculty)) {
    $errors['staff_location'] = 'Please select either a Unit/Office OR Faculty';
}

// Note: Department is optional for non-teaching staff at faculty level
// They can work at faculty level without being assigned to a specific department
```

### 2. Frontend Validation (`app/Views/auth/register.php`)

**Updated Help Text:**
```
Before: "Select where you work - either a Unit/Office OR a Faculty/Department"
After:  "Select where you work - either a Unit/Office OR a Faculty (Department is optional)"
```

**Updated Error Message:**
```
Before: "Please select either a Unit/Office OR a Faculty/Department"
After:  "Please select either a Unit/Office OR a Faculty"
```

**Updated JavaScript Validation:**
```javascript
// Before
if (!unit && !faculty && !department) {
    alert('Non-teaching staff must select either a Unit/Office OR a Faculty/Department.');
    return false;
}

// After
if (!unit && !faculty) {
    alert('Non-teaching staff must select either a Unit/Office OR a Faculty.');
    return false;
}
```

## Use Cases Now Supported

### Non-Teaching Staff Registration Options:

1. **Unit/Office Staff**
   - Select: Unit/Office
   - Faculty: Empty
   - Department: Empty
   - Example: Registry staff, Bursary staff, Library staff

2. **Faculty-Level Staff** (NEW - Now Supported)
   - Select: Faculty
   - Unit: Empty
   - Department: Empty
   - Example: Faculty Secretary, Faculty Administrator, Faculty Accountant

3. **Department-Level Staff**
   - Select: Faculty + Department
   - Unit: Empty
   - Example: Department Secretary, Department Assistant

## Testing

### Test Case 1: Faculty-Level Non-Teaching Staff
1. Go to registration page
2. Select "Non-Teaching Staff"
3. Select a Faculty (e.g., "Faculty of Science")
4. Leave Department empty
5. Leave Unit empty
6. Complete other required fields
7. Submit form
8. ✅ Should register successfully

### Test Case 2: Unit Staff
1. Select "Non-Teaching Staff"
2. Select a Unit (e.g., "Registry")
3. Leave Faculty and Department empty
4. ✅ Should register successfully

### Test Case 3: Department Staff
1. Select "Non-Teaching Staff"
2. Select Faculty and Department
3. Leave Unit empty
4. ✅ Should register successfully

### Test Case 4: Nothing Selected (Should Fail)
1. Select "Non-Teaching Staff"
2. Leave Unit, Faculty, and Department all empty
3. ❌ Should show error: "Please select either a Unit/Office OR a Faculty"

## Database Storage

The profile will be stored with:
- `staff_type`: "non-teaching"
- `faculty`: Selected faculty name (e.g., "Faculty of Science")
- `department`: NULL or empty
- `unit`: NULL or empty

## Impact

This fix allows:
- ✅ Faculty secretaries to register
- ✅ Faculty administrators to register
- ✅ Faculty accountants to register
- ✅ Any non-teaching staff working at faculty level without department assignment

## Files Modified

1. `app/Controllers/AuthController.php` - Backend validation
2. `app/Views/auth/register.php` - Frontend validation and help text

## Validation Status

✅ No PHP syntax errors
✅ No diagnostics issues
✅ Backend validation updated
✅ Frontend validation updated
✅ Help text updated
✅ Error messages updated

## Ready for Testing

The fix is complete and ready for testing. Non-teaching staff can now register with just a faculty selection without being required to select a department.
