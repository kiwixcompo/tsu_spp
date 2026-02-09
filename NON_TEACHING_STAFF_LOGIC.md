# Non-Teaching Staff Selection Logic

## Overview
Non-teaching staff have flexibility in selecting where they work. They can choose EITHER:
- A Unit/Office/Directorate, OR
- A Faculty + Department

This reflects the reality that some non-teaching staff work in administrative units while others work within academic departments.

---

## Selection Options

### Option 1: Unit/Office/Directorate
For staff working in administrative/support units such as:
- Office of the Vice Chancellor
- ICT
- Bursary Department
- Security Division
- Library
- etc. (51 units total)

**Fields:**
- Unit: Select from dropdown (51 options)
- Faculty: Not required
- Department: Not required

### Option 2: Faculty + Department
For staff working within academic departments such as:
- Department secretaries
- Lab technicians
- Department assistants
- etc.

**Fields:**
- Unit: Not required
- Faculty: Select from dropdown
- Department: Select from dropdown (based on faculty)

---

## Validation Rules

### Teaching Staff:
- ✅ Faculty: **Required**
- ✅ Department: **Required**
- ❌ Unit: Not applicable

### Non-Teaching Staff:
Must select ONE of the following:

**Option A: Unit Only**
- ✅ Unit: Selected
- ❌ Faculty: Empty
- ❌ Department: Empty

**Option B: Faculty + Department**
- ❌ Unit: Empty
- ✅ Faculty: Selected
- ✅ Department: Selected

**Invalid:**
- ❌ Nothing selected (error)
- ❌ Faculty selected but no department (error)
- ⚠️ Both unit AND faculty/department selected (unit takes precedence, faculty/department cleared)

---

## User Interface Behavior

### When Non-Teaching Staff is Selected:

1. **Unit Dropdown** appears with label: "Unit/Office/Directorate (Optional)"
2. **"OR" Badge** displayed between sections
3. **Faculty Dropdown** appears with label: "Faculty (Optional)"
4. **Department Dropdown** appears with label: "Department (Optional)"

### Auto-Clear Logic:

**If user selects a Unit:**
- Faculty dropdown automatically clears
- Department dropdown automatically clears and disables
- This ensures only unit is selected

**If user selects a Faculty:**
- Unit dropdown automatically clears
- Department dropdown enables
- User must then select department

**If user selects Faculty but not Department:**
- Form validation shows error
- "Please select a department for the selected faculty"

**If user selects nothing:**
- Form validation shows error
- "Non-teaching staff must select either a Unit/Office OR a Faculty/Department"

---

## Database Storage

### profiles table columns:
- `staff_type`: ENUM('teaching', 'non-teaching')
- `unit`: VARCHAR(255) NULL - Stores unit name if selected
- `faculty`: VARCHAR(255) NULL - Stores faculty name
- `department`: VARCHAR(255) NULL - Stores department name

### Data Examples:

**Teaching Staff:**
```
staff_type: 'teaching'
unit: NULL
faculty: 'Faculty of Science'
department: 'Computer Science'
```

**Non-Teaching Staff (Unit):**
```
staff_type: 'non-teaching'
unit: 'ICT'
faculty: NULL
department: NULL
```

**Non-Teaching Staff (Department):**
```
staff_type: 'non-teaching'
unit: NULL
faculty: 'Faculty of Science'
department: 'Computer Science'
```

---

## Form Field Names

### Teaching Staff Fields:
- `staff_type`: 'teaching'
- `faculty`: Required
- `department`: Required

### Non-Teaching Staff Fields:
- `staff_type`: 'non-teaching'
- `unit`: Optional (but one of unit OR faculty/dept required)
- `faculty_nt`: Optional (but one of unit OR faculty/dept required)
- `department_nt`: Optional (required if faculty_nt selected)

**Note:** Field names are different (`faculty_nt`, `department_nt`) to distinguish from teaching staff fields in form processing.

---

## Controller Processing Logic

### AuthController - register() method:

```php
$staffType = $this->input('staff_type');

if ($staffType === 'teaching') {
    // Validate teaching staff
    $errors = $this->validate([
        'faculty' => 'required',
        'department' => 'required',
    ]);
    
    $faculty = $this->input('faculty');
    $department = $this->input('department');
    $unit = null;
    
} else {
    // Validate non-teaching staff
    $unit = $this->input('unit');
    $faculty = $this->input('faculty_nt');
    $department = $this->input('department_nt');
    
    // Must have either unit OR faculty/department
    if (empty($unit) && empty($faculty) && empty($department)) {
        $errors['staff_location'] = 'Please select either a unit or faculty/department';
    }
    
    // If faculty selected, department must also be selected
    if (!empty($faculty) && empty($department)) {
        $errors['department'] = 'Please select a department for the selected faculty';
    }
}

// Store in session
$_SESSION['registration_data'] = [
    'staff_type' => $staffType,
    'unit' => $unit,
    'faculty' => $faculty,
    'department' => $department,
];
```

---

## Profile Display Logic

### When displaying profiles:

**Teaching Staff:**
```
Staff Type: Teaching Staff
Faculty: Faculty of Science
Department: Computer Science
```

**Non-Teaching Staff (Unit):**
```
Staff Type: Non-Teaching Staff
Unit: ICT Department
```

**Non-Teaching Staff (Department):**
```
Staff Type: Non-Teaching Staff
Faculty: Faculty of Science
Department: Computer Science
```

---

## Search & Filter Logic

### Directory Search:
Users can search by:
- Name
- Faculty (for both teaching and non-teaching in departments)
- Department (for both teaching and non-teaching in departments)
- Unit (for non-teaching in units)

### Admin Filters:
- Filter by Staff Type (Teaching/Non-Teaching)
- Filter by Faculty (shows both teaching and non-teaching in that faculty)
- Filter by Department (shows both teaching and non-teaching in that department)
- Filter by Unit (shows only non-teaching in that unit)

---

## ID Card Display

### Teaching Staff ID Card:
```
Name: Dr. John Doe
Designation: Senior Lecturer
Staff ID: TSU/SP/123
Faculty: Faculty of Science
Department: Computer Science
```

### Non-Teaching Staff ID Card (Unit):
```
Name: Jane Smith
Designation: ICT Officer
Staff ID: TSU/SP/456
Unit: ICT Department
```

### Non-Teaching Staff ID Card (Department):
```
Name: Mary Johnson
Designation: Lab Technician
Staff ID: TSU/SP/789
Faculty: Faculty of Science
Department: Computer Science
```

---

## Benefits of This Approach

1. **Flexibility:** Accommodates different organizational structures
2. **Accuracy:** Reflects actual work locations
3. **Simplicity:** Clear either/or choice
4. **Validation:** Prevents incomplete data
5. **Searchability:** Easy to find staff by location
6. **Reporting:** Can generate reports by unit or department

---

## Testing Scenarios

### Test 1: Teaching Staff
- [x] Select "Teaching Staff"
- [x] Faculty and Department fields appear (required)
- [x] Select faculty
- [x] Select department
- [x] Submit form
- [x] Data saves correctly

### Test 2: Non-Teaching Staff (Unit)
- [x] Select "Non-Teaching Staff"
- [x] Unit, Faculty, Department fields appear (optional)
- [x] Select unit
- [x] Faculty/Department auto-clear
- [x] Submit form
- [x] Data saves with unit only

### Test 3: Non-Teaching Staff (Department)
- [x] Select "Non-Teaching Staff"
- [x] Select faculty
- [x] Unit auto-clears
- [x] Department dropdown enables
- [x] Select department
- [x] Submit form
- [x] Data saves with faculty/department

### Test 4: Validation Errors
- [x] Non-teaching staff selects nothing → Error shown
- [x] Non-teaching staff selects faculty but not department → Error shown
- [x] Form prevents submission until valid selection made

---

## Summary

This implementation provides maximum flexibility for non-teaching staff while maintaining data integrity through proper validation. The either/or logic ensures that every non-teaching staff member has a clear work location recorded, whether it's an administrative unit or an academic department.
