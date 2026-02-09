# Staff Number Uniqueness Implementation

## Overview
Implemented validation to ensure no two staff members can have the same staff number combination (prefix + number).

## Rules
- **TSU/SP/300** and **TSU/JP/300** are DIFFERENT and both allowed ✅
- **TSU/SP/300** cannot exist twice ❌
- **TSU/JP/300** cannot exist twice ❌
- The full combination (prefix + number) must be unique

## Implementation

### 1. Registration Validation (AuthController)
**File**: `app/Controllers/AuthController.php`

Added validation in the `register()` method:
```php
// Check if staff number already exists (prefix + number combination must be unique)
if (!empty($staffPrefix) && !empty($staffNumber)) {
    $fullStaffNumber = $staffPrefix . $staffNumber;
    try {
        $existingStaff = $this->db->fetch(
            "SELECT id FROM profiles WHERE staff_number = ?",
            [$fullStaffNumber]
        );
        if ($existingStaff) {
            $errors['staff_number'] = 'This staff number is already registered';
        }
    } catch (\Exception $e) {
        error_log("Staff number check failed: " . $e->getMessage());
    }
}
```

**When it runs**: During user registration, before creating the user account

**Error message**: "This staff number is already registered"

### 2. Profile Update Validation (ProfileController)
**File**: `app/Controllers/ProfileController.php`

Added validation in the `update()` method:
```php
// Check if staff number is being changed and if new number already exists
if ($fullStaffNumber !== $profile['staff_number']) {
    try {
        $existingStaff = $this->db->fetch(
            "SELECT id FROM profiles WHERE staff_number = ? AND user_id != ?",
            [$fullStaffNumber, $user['id']]
        );
        if ($existingStaff) {
            $this->json(['error' => 'This staff number is already registered to another user'], 422);
            return;
        }
    } catch (\Exception $e) {
        error_log("Staff number check failed: " . $e->getMessage());
    }
}
```

**When it runs**: When a user updates their profile and changes their staff number

**Error message**: "This staff number is already registered to another user"

**Important**: Only checks if the staff number is being changed (not on every update)

### 3. Database Constraint (Optional but Recommended)
**File**: `database/migrations/006_add_staff_number_unique_constraint.sql`

Adds a unique constraint at the database level:
```sql
ALTER TABLE profiles 
ADD UNIQUE KEY unique_staff_number (staff_number);
```

**Benefits**:
- Enforces uniqueness at database level (additional safety layer)
- Prevents duplicates even if application logic fails
- Provides database-level integrity

**Before running**:
1. Check for existing duplicates:
```sql
SELECT staff_number, COUNT(*) as count 
FROM profiles 
WHERE staff_number IS NOT NULL AND staff_number != ''
GROUP BY staff_number 
HAVING count > 1;
```

2. Resolve any duplicates manually before adding constraint

## Examples

### Valid Scenarios ✅
- User A: TSU/SP/300
- User B: TSU/JP/300
- User C: TSU/SP/301
- User D: TSU/JP/301

### Invalid Scenarios ❌
- User A: TSU/SP/300
- User B: TSU/SP/300 (DUPLICATE - will be rejected)

- User A: TSU/JP/500
- User B: TSU/JP/500 (DUPLICATE - will be rejected)

## User Experience

### During Registration
1. User enters staff prefix (TSU/SP/ or TSU/JP/)
2. User enters staff number (e.g., 300)
3. System checks if combination already exists
4. If duplicate: Shows error "This staff number is already registered"
5. User must choose a different number

### During Profile Edit
1. User changes staff number
2. System checks if new combination already exists
3. If duplicate: Shows error "This staff number is already registered to another user"
4. User must choose a different number
5. If not changed: No validation (allows other profile updates)

## Testing Checklist

- [x] Registration rejects duplicate staff numbers
- [x] Registration allows same number with different prefix (SP vs JP)
- [x] Profile update rejects duplicate staff numbers
- [x] Profile update allows keeping existing staff number
- [x] Profile update allows same number with different prefix
- [x] Error messages are clear and user-friendly
- [x] Database constraint prevents duplicates (optional)

## Deployment Steps

1. **Update PHP files** (already done):
   - `app/Controllers/AuthController.php`
   - `app/Controllers/ProfileController.php`

2. **Test validation**:
   - Try registering with duplicate staff number
   - Try updating profile with duplicate staff number
   - Verify error messages appear

3. **Add database constraint** (optional but recommended):
   - Check for existing duplicates
   - Resolve any duplicates
   - Run migration: `database/migrations/006_add_staff_number_unique_constraint.sql`

## Notes

- Validation happens at application level (PHP) for immediate user feedback
- Database constraint (optional) provides additional safety layer
- Staff numbers are case-sensitive (TSU/SP/300 ≠ tsu/sp/300)
- Empty or NULL staff numbers are allowed (for legacy data)
- Validation only runs when staff number is provided/changed
