#!/usr/bin/env php
<?php
/**
 * Verification script for TSU Staff Portal - Session 3 Updates
 * Checks if all changes are properly applied
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n";
echo "╔" . str_repeat("═", 78) . "╗\n";
echo "║" . str_pad("TSU Staff Portal - Session 3 Verification", 78) . "║\n";
echo "╚" . str_repeat("═", 78) . "╝\n";
echo "\n";

$projectRoot = dirname(__FILE__);
$checks = [];

// Check 1: Verify database migration
echo "[1/6] Checking database migration...\n";
try {
    require_once $projectRoot . '/app/Core/Database.php';
    $db = \App\Core\Database::getInstance();
    $result = $db->fetch("SHOW COLUMNS FROM education LIKE 'display_years'");
    
    if ($result) {
        echo "  ✓ display_years column exists in education table\n";
        $checks['db_column'] = true;
    } else {
        echo "  ✗ display_years column NOT found - migration needs to be run\n";
        $checks['db_column'] = false;
    }
} catch (\Exception $e) {
    echo "  ✗ Database check failed: " . $e->getMessage() . "\n";
    $checks['db_column'] = false;
}

// Check 2: Verify DirectoryController updates
echo "\n[2/6] Checking DirectoryController admin filtering...\n";
$controllerFile = $projectRoot . '/app/Controllers/DirectoryController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    if (strpos($content, "u.role != 'admin'") !== false) {
        echo "  ✓ Admin filtering found in DirectoryController\n";
        $checks['dir_controller'] = true;
    } else {
        echo "  ✗ Admin filtering NOT found in DirectoryController\n";
        $checks['dir_controller'] = false;
    }
} else {
    echo "  ✗ DirectoryController file not found\n";
    $checks['dir_controller'] = false;
}

// Check 3: Verify directory profile view updates
echo "\n[3/6] Checking directory profile view for year display condition...\n";
$profileFile = $projectRoot . '/app/Views/directory/profile.php';
if (file_exists($profileFile)) {
    $content = file_get_contents($profileFile);
    if (strpos($content, "display_years") !== false && strpos($content, "== 1") !== false) {
        echo "  ✓ Conditional year display found in profile view\n";
        $checks['profile_view'] = true;
    } else {
        echo "  ✗ Conditional year display NOT found in profile view\n";
        $checks['profile_view'] = false;
    }
} else {
    echo "  ✗ Profile view file not found\n";
    $checks['profile_view'] = false;
}

// Check 4: Verify education form checkbox
echo "\n[4/6] Checking education form for display_years checkbox...\n";
$educationFormFile = $projectRoot . '/app/Views/profile/education.php';
if (file_exists($educationFormFile)) {
    $content = file_get_contents($educationFormFile);
    if (strpos($content, 'display_years') !== false && strpos($content, 'Show year range in public profile') !== false) {
        echo "  ✓ display_years checkbox found in education form\n";
        $checks['education_form'] = true;
    } else {
        echo "  ✗ display_years checkbox NOT found in education form\n";
        $checks['education_form'] = false;
    }
} else {
    echo "  ✗ Education form file not found\n";
    $checks['education_form'] = false;
}

// Check 5: Verify ProfileController addEducation update
echo "\n[5/6] Checking ProfileController::addEducation() for display_years handling...\n";
$profileControllerFile = $projectRoot . '/app/Controllers/ProfileController.php';
if (file_exists($profileControllerFile)) {
    $content = file_get_contents($profileControllerFile);
    if (preg_match("/addEducation.*'display_years'.*\$this->input\('display_years'\)/s", $content)) {
        echo "  ✓ display_years handling found in addEducation()\n";
        $checks['add_education'] = true;
    } else {
        echo "  ✗ display_years handling NOT found in addEducation()\n";
        $checks['add_education'] = false;
    }
} else {
    echo "  ✗ ProfileController file not found\n";
    $checks['add_education'] = false;
}

// Check 6: Verify ProfileController updateEducation update
echo "\n[6/6] Checking ProfileController::updateEducation() for display_years handling...\n";
if (file_exists($profileControllerFile)) {
    $content = file_get_contents($profileControllerFile);
    if (preg_match("/updateEducation.*'display_years'.*\$this->input\('display_years'\)/s", $content)) {
        echo "  ✓ display_years handling found in updateEducation()\n";
        $checks['update_education'] = true;
    } else {
        echo "  ✗ display_years handling NOT found in updateEducation()\n";
        $checks['update_education'] = false;
    }
} else {
    echo "  ✗ ProfileController file not found\n";
    $checks['update_education'] = false;
}

// Summary
echo "\n";
echo "╔" . str_repeat("═", 78) . "╗\n";

$passed = array_sum($checks);
$total = count($checks);

if ($passed === $total) {
    echo "║" . str_pad("✓ ALL CHECKS PASSED ($passed/$total)", 78) . "║\n";
} else {
    echo "║" . str_pad("✗ SOME CHECKS FAILED ($passed/$total)", 78) . "║\n";
}

echo "╚" . str_repeat("═", 78) . "╝\n";

// Detailed results
echo "\nDetailed Results:\n";
echo "  • Database migration applied: " . ($checks['db_column'] ? "✓" : "✗ PENDING") . "\n";
echo "  • DirectoryController updated: " . ($checks['dir_controller'] ? "✓" : "✗") . "\n";
echo "  • Profile view conditional: " . ($checks['profile_view'] ? "✓" : "✗") . "\n";
echo "  • Education form checkbox: " . ($checks['education_form'] ? "✓" : "✗") . "\n";
echo "  • addEducation() updated: " . ($checks['add_education'] ? "✓" : "✗") . "\n";
echo "  • updateEducation() updated: " . ($checks['update_education'] ? "✓" : "✗") . "\n";

echo "\n";
if (!$checks['db_column']) {
    echo "⚠️  REQUIRED ACTION: Run the database migration\n";
    echo "   Execute: ALTER TABLE education ADD COLUMN display_years BOOLEAN DEFAULT FALSE AFTER description;\n";
}

echo "\n";
?>
