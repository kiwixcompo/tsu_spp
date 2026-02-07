<?php
/**
 * Debug profile setup errors
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

echo "<h2>Profile Setup Debug</h2>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>ERROR: No user logged in</p>";
    exit;
}

echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";

$db = Database::getInstance();

// Check user
$user = $db->fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
if (!$user) {
    echo "<p style='color: red;'>ERROR: User not found</p>";
    exit;
}

echo "<p>User Email: " . htmlspecialchars($user['email']) . "</p>";
echo "<p>Account Status: " . htmlspecialchars($user['account_status']) . "</p>";
echo "<p>Email Verified: " . ($user['email_verified'] ? 'Yes' : 'No') . "</p>";

// Check if profile exists
$profile = $db->fetch("SELECT * FROM profiles WHERE user_id = ?", [$_SESSION['user_id']]);
if ($profile) {
    echo "<p style='color: orange;'>WARNING: Profile already exists!</p>";
    echo "<pre>" . print_r($profile, true) . "</pre>";
} else {
    echo "<p style='color: green;'>No profile exists yet (good for setup)</p>";
}

// Check registration data
if (isset($_SESSION['registration_data'])) {
    echo "<h3>Registration Data in Session:</h3>";
    echo "<pre>" . print_r($_SESSION['registration_data'], true) . "</pre>";
} else {
    echo "<p style='color: orange;'>No registration data in session</p>";
}

// Test profile model
echo "<h3>Testing Profile Model:</h3>";
try {
    require_once __DIR__ . '/app/Models/Profile.php';
    $profileModel = new App\Models\Profile();
    echo "<p style='color: green;'>Profile model loaded successfully</p>";
    
    // Test slug generation
    $testSlug = $profileModel->generateUniqueSlug('test-user');
    echo "<p>Test slug generated: " . htmlspecialchars($testSlug) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR loading profile model: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Check uploads directory
$uploadsDir = __DIR__ . '/public/uploads/profiles/';
if (!is_dir($uploadsDir)) {
    echo "<p style='color: orange;'>Uploads directory doesn't exist: $uploadsDir</p>";
    echo "<p>Attempting to create...</p>";
    if (mkdir($uploadsDir, 0755, true)) {
        echo "<p style='color: green;'>Directory created successfully</p>";
    } else {
        echo "<p style='color: red;'>Failed to create directory</p>";
    }
} else {
    echo "<p style='color: green;'>Uploads directory exists</p>";
    echo "<p>Writable: " . (is_writable($uploadsDir) ? 'Yes' : 'No') . "</p>";
}

echo "<hr>";
echo "<p><a href='profile/setup'>Go to Profile Setup</a></p>";
