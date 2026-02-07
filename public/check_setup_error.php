<?php
/**
 * Check what's causing the setup error
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

echo "<h2>Setup Error Diagnostic</h2>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>ERROR: No user logged in. Please log in first.</p>";
    echo "<p><a href='../login'>Go to Login</a></p>";
    exit;
}

echo "<p>User ID in session: " . $_SESSION['user_id'] . "</p>";

// Check database connection
require_once __DIR__ . '/../app/Core/Database.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Database connection OK</p>";
    
    // Check user
    $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    if ($user) {
        echo "<p style='color: green;'>✓ User found</p>";
        echo "<ul>";
        echo "<li>Email: " . htmlspecialchars($user['email']) . "</li>";
        echo "<li>Status: " . htmlspecialchars($user['account_status']) . "</li>";
        echo "<li>Email Verified: " . ($user['email_verified'] ? 'Yes' : 'No') . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ User not found in database</p>";
    }
    
    // Check profile
    $profile = $db->fetch("SELECT * FROM profiles WHERE user_id = ?", [$_SESSION['user_id']]);
    if ($profile) {
        echo "<p style='color: orange;'>⚠ Profile already exists</p>";
        echo "<p>You should be redirected to dashboard, not setup page.</p>";
    } else {
        echo "<p style='color: green;'>✓ No profile exists (ready for setup)</p>";
    }
    
    // Check error log location
    echo "<h3>Error Log Location</h3>";
    echo "<p>Check your PHP error log for detailed errors:</p>";
    echo "<ul>";
    echo "<li>WAMP: C:\\wamp64\\logs\\php_error.log</li>";
    echo "<li>Or check: " . ini_get('error_log') . "</li>";
    echo "</ul>";
    
    echo "<h3>Test Setup Submission</h3>";
    echo "<p>Try submitting the setup form and check the error log for messages starting with '=== Profile Setup Started ==='</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='../profile/setup'>Go to Profile Setup</a></p>";
