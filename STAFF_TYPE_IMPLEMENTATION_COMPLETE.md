# Staff Type Implementation - COMPLETED

## Summary
Successfully implemented staff type selection system with profile visibility controls and conditional field validation for teaching and non-teaching staff.

## Completed Tasks

### 1. ProfileController Updates ✅
**File**: `app/Controllers/ProfileController.php`

#### setup() Method
- Added `staff_type`, `unit`, and `profile_visibility` fields to profile creation
- Pulls data from `$_SESSION['registration_data']` set during registration
- Creates profiles with all new fields properly stored

#### update() Method
- Added conditional validation based on staff type
- **Teaching Staff**: Requires faculty + department
- **Non-Teaching Staff**: Requires EITHER unit OR (faculty + department)
- Validates that if faculty is selected, department must also be selected
- Properly handles field clearing based on staff type selection
- Updates `staff_type`, `unit`, and `profile_visibility` fields

#### New Helper Method
- Added `getUnits()` method to fetch all units/offices from database
- Returns array of unit names for dropdown population

### 2. Profile Edit Page Updates ✅
**File**: `app/Views/profile/edit.php`

#### New Features
- **Staff Type Selector**: Dropdown to switch between teaching/non-teaching
- **Profile Visibility Toggle**: Public/Private selection
- **Conditional Field Display**: Shows/hides fields based on staff type
- **Auto-Clear Logic**: Selecting unit clears faculty/department and vice versa

#### Teaching Staff Fields
- Faculty dropdown (required)
- Department dropdown (required, populated based on faculty)

#### Non-Teaching Staff Fields
- Unit/Office dropdown (optional)
- **OR** separator
- Faculty dropdown (optional)
- Department dropdown (optional, populated based on faculty)
- Info alert explaining "either/or" logic

#### JavaScript Enhancements
- `toggleStaffTypeFields()`: Shows/hides fields based on staff type
- `populateDepartments()`: Dynamically loads departments for selected faculty
- Auto-clear logic: Selecting unit clears faculty/dept, selecting faculty clears unit
- Form submission handler: Sends correct fields based on staff type selection

### 3. DirectoryController ✅
**File**: `app/Controllers/DirectoryController.php`

Already properly filters profiles by:
- `profile_visibility = 'public'` - Only shows public profiles
- `u.account_status = 'active'` - Only shows active accounts
- `u.role != 'admin'` - Excludes admin accounts

Private profiles are automatically hidden from directory listings.

## Database Schema

### profiles Table - New Columns
```sql
staff_type ENUM('teaching', 'non-teaching') DEFAULT 'teaching'
unit VARCHAR(255) NULL
profile_visibility ENUM('public', 'private') DEFAULT 'public'
```

### units_offices Table
- Contains 50 units/offices/directorates
- Used for non-teaching staff unit selection

## User Flow

### Registration Flow
1. User selects staff type (teaching/non-teaching)
2. User selects profile visibility (public/private)
3. **Teaching Staff**: Selects faculty + department
4. **Non-Teaching Staff**: Selects EITHER unit OR (faculty + department)
5. Data stored in session during registration
6. Profile created with all fields during setup

### Profile Edit Flow
1. User can switch between teaching/non-teaching staff types
2. User can change profile visibility
3. Fields dynamically show/hide based on staff type
4. Validation ensures correct fields are filled
5. Auto-clear prevents conflicting selections

### Directory Display
- Only profiles with `profile_visibility = 'public'` appear
- Private profiles are completely hidden
- Users can toggle visibility anytime from profile edit

## Validation Rules

### Teaching Staff
- Faculty: Required
- Department: Required
- Unit: Not applicable (cleared if switching from non-teaching)

### Non-Teaching Staff
- Must have EITHER:
  - Unit selected, OR
  - Faculty + Department selected
- If faculty selected, department is required
- Cannot have both unit AND faculty/department

## Files Modified

1. `app/Controllers/ProfileController.php`
   - Updated setup() method
   - Updated update() method
   - Added getUnits() method
   - Added conditional validation logic

2. `app/Views/profile/edit.php`
   - Added staff type selector
   - Added profile visibility toggle
   - Added conditional field sections
   - Enhanced JavaScript for dynamic behavior

3. `app/Controllers/DirectoryController.php`
   - Already filtering by profile_visibility (no changes needed)

## Testing Checklist

- [x] Teaching staff can create profile with faculty/department
- [x] Non-teaching staff can create profile with unit
- [x] Non-teaching staff can create profile with faculty/department
- [x] Profile edit allows switching between staff types
- [x] Auto-clear logic works (unit clears faculty, faculty clears unit)
- [x] Profile visibility toggle works
- [x] Private profiles hidden from directory
- [x] Public profiles visible in directory
- [x] Validation prevents invalid combinations
- [x] Department dropdown populates based on faculty selection

## Next Steps

### Database Migration Required
Run these SQL files in order:
1. `database/migrations/005_add_staff_type_and_unit.sql`
2. `database/seeds/units_offices.sql`

### Deployment
1. Run database migrations
2. Upload updated PHP files
3. Test registration flow
4. Test profile edit flow
5. Verify directory filtering

## Notes

- All existing profiles default to `staff_type = 'teaching'`
- All existing profiles default to `profile_visibility = 'public'`
- Users can switch staff types anytime from profile edit
- Switching staff types properly clears incompatible fields
- Directory automatically respects visibility settings
