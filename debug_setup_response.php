<?php
/**
 * Debug Setup Response
 * This script will help identify what's being returned from the setup endpoint
 */

// Check if we can access the setup endpoint
$setupUrl = 'http://localhost/tsu_spp/public/profile/setup';

echo "=== Debugging Profile Setup Response ===\n\n";

// Check if curl is available
if (!function_exists('curl_init')) {
    echo "ERROR: cURL is not available. Please enable it in php.ini\n";
    exit(1);
}

// First, let's check what happens when we access the endpoint
echo "1. Testing GET request to setup page...\n";
$ch = curl_init($setupUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";
echo "   Response length: " . strlen($response) . " bytes\n\n";

// Check for common issues
echo "2. Checking for common issues:\n";

// Check if profiles table exists
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // Check profiles table
    $result = $db->query("SHOW TABLES LIKE 'profiles'");
    if ($result->rowCount() > 0) {
        echo "   ✓ Profiles table exists\n";
        
        // Check table structure
        $columns = $db->fetchAll("DESCRIBE profiles");
        echo "   ✓ Profiles table has " . count($columns) . " columns\n";
        
        // Check for required columns
        $requiredColumns = ['user_id', 'title', 'first_name', 'last_name', 'profile_slug'];
        $existingColumns = array_column($columns, 'Field');
        
        foreach ($requiredColumns as $col) {
            if (in_array($col, $existingColumns)) {
                echo "   ✓ Column '$col' exists\n";
            } else {
                echo "   ✗ Column '$col' is MISSING!\n";
            }
        }
    } else {
        echo "   ✗ Profiles table does NOT exist!\n";
    }
    
    echo "\n3. Checking FileUploadHelper:\n";
    if (class_exists('App\Helpers\FileUploadHelper')) {
        echo "   ✓ FileUploadHelper class exists\n";
    } else {
        echo "   ✗ FileUploadHelper class NOT found!\n";
    }
    
    echo "\n4. Checking upload directory:\n";
    $uploadDir = __DIR__ . '/storage/uploads/profile_photos';
    if (is_dir($uploadDir)) {
        echo "   ✓ Upload directory exists: $uploadDir\n";
        if (is_writable($uploadDir)) {
            echo "   ✓ Upload directory is writable\n";
        } else {
            echo "   ✗ Upload directory is NOT writable!\n";
        }
    } else {
        echo "   ✗ Upload directory does NOT exist: $uploadDir\n";
        echo "   Attempting to create...\n";
        if (mkdir($uploadDir, 0755, true)) {
            echo "   ✓ Created upload directory\n";
        } else {
            echo "   ✗ Failed to create upload directory\n";
        }
    }
    
} catch (\Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
