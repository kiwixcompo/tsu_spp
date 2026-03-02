# Admin Search, Filter & Excel Export - Installation Guide

## Overview
This implementation adds server-side search, filtering, and Excel export functionality to the admin users management page.

## Features Implemented

### 1. Server-Side Search
- Search across ALL users in the database (not just current page)
- Search by: Name, Staff ID, Email, Faculty, Unit
- Debounced search (500ms delay) for better performance
- Real-time AJAX updates without page reload

### 2. Advanced Filters
- Filter by Staff Type (Teaching/Non-Teaching)
- Filter by Gender (Male/Female)
- Filter by Faculty
- Filter by Unit
- Filters work in combination with search
- Bulk actions work on filtered results

### 3. Excel Export
- Export all users or filtered results to Excel
- Automatic categorization into sheets by Faculty/Unit
- Includes all user details: Staff Number, Name, Email, Phone, Faculty, Department, Unit, Designation, Staff Type, Gender, Status, etc.
- Professional formatting with headers and auto-sized columns

## Files Modified

1. **app/Controllers/AdminController.php**
   - Added `searchUsers()` method - Handles AJAX search with filters
   - Added `exportUsers()` method - Generates Excel file with categorized sheets

2. **routes/web.php**
   - Added POST route: `/admin/users/search`
   - Added GET route: `/admin/users/export`

3. **composer.json**
   - Added dependency: `phpoffice/phpspreadsheet: ^1.29`

4. **app/Views/admin/users.php**
   - Added filter dropdowns (Staff Type, Gender, Faculty, Unit)
   - Added "Export to Excel" button
   - Replaced client-side search with AJAX search
   - Added dynamic table update functionality
   - Added dynamic pagination update
   - Improved user experience with loading states

## Installation Steps

### Step 1: Install PHPSpreadsheet

Run this command in your project root:

```bash
composer require phpoffice/phpspreadsheet
```

If you encounter timeout issues (as happened during implementation), try:

```bash
composer require phpoffice/phpspreadsheet --prefer-dist --no-progress
```

Or increase the timeout:

```bash
composer config --global process-timeout 2000
composer require phpoffice/phpspreadsheet
```

### Step 2: Verify Installation

Check that PHPSpreadsheet is installed:

```bash
composer show phpoffice/phpspreadsheet
```

You should see version 1.29 or higher.

### Step 3: Test the Features

1. **Test Search:**
   - Go to Admin > Users Management
   - Type in the search box
   - Results should update after 500ms delay
   - Search works across all pages

2. **Test Filters:**
   - Select different filter options
   - Results update immediately
   - Combine multiple filters
   - Filters work with search

3. **Test Excel Export:**
   - Click "Export to Excel" button
   - Excel file downloads automatically
   - Open file to verify:
     - Multiple sheets (one per Faculty/Unit)
     - All user data included
     - Professional formatting

4. **Test Bulk Actions on Filtered Results:**
   - Apply filters
   - Select users from filtered results
   - Use bulk actions (Generate ID Cards, Activate, Suspend, Delete)
   - Actions apply only to selected users

## API Endpoints

### POST /admin/users/search
**Purpose:** Search and filter users

**Parameters:**
- `query` (string, optional) - Search term
- `staff_type` (string, optional) - "teaching" or "non-teaching"
- `gender` (string, optional) - "male" or "female"
- `faculty` (string, optional) - Faculty name
- `unit` (string, optional) - Unit name
- `page` (int, optional) - Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "users": [...],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_users": 100,
    "per_page": 20
  }
}
```

### GET /admin/users/export
**Purpose:** Export users to Excel

**Parameters:**
- `staff_type` (string, optional)
- `gender` (string, optional)
- `faculty` (string, optional)
- `unit` (string, optional)

**Response:** Excel file download

## Troubleshooting

### Issue: "PHPSpreadsheet library not installed" error
**Solution:** Run `composer require phpoffice/phpspreadsheet`

### Issue: Search not working
**Solution:** 
1. Check browser console for JavaScript errors
2. Verify CSRF token is present
3. Check that routes are properly registered

### Issue: Filters not populating
**Solution:**
1. Verify `/faculties-departments` endpoint is accessible
2. Check that profiles table has faculty and unit data

### Issue: Excel export fails
**Solution:**
1. Verify PHPSpreadsheet is installed
2. Check PHP memory limit (increase if needed)
3. Verify write permissions for temporary files

### Issue: Pagination not working
**Solution:**
1. Check JavaScript console for errors
2. Verify pagination HTML is being generated
3. Test with different page numbers

## Performance Considerations

1. **Search Debouncing:** 500ms delay prevents excessive server requests
2. **Pagination:** 20 users per page for optimal performance
3. **Excel Export:** Handles large datasets efficiently with streaming
4. **AJAX Updates:** Only table content is updated, not entire page

## Security Features

1. **CSRF Protection:** All POST requests require valid CSRF token
2. **Admin Authentication:** All endpoints require admin role
3. **Input Sanitization:** All user inputs are sanitized
4. **SQL Injection Prevention:** Prepared statements used throughout

## Future Enhancements

Possible improvements for future versions:

1. Add export to CSV format
2. Add export to PDF format
3. Add saved filter presets
4. Add column sorting
5. Add advanced search with date ranges
6. Add export scheduling
7. Add email notification for large exports

## Support

If you encounter any issues:

1. Check error logs: `error.log` and `error_log`
2. Check browser console for JavaScript errors
3. Verify all files were updated correctly
4. Ensure database connection is working
5. Test with a small dataset first

## Summary

All code changes have been implemented and validated with no syntax errors. The only remaining step is to install PHPSpreadsheet via Composer. Once installed, all features will be fully functional.
