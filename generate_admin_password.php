<?php
/**
 * Generate Admin Password Hash
 * Run this to generate a new password hash for the admin user
 * Usage: php generate_admin_password.php
 */

echo "═══════════════════════════════════════════════════════════\n";
echo "  Admin Password Hash Generator\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Generate hash for Admin123!
$password1 = 'Admin123!';
$hash1 = password_hash($password1, PASSWORD_DEFAULT);

echo "Password: $password1\n";
echo "Hash: $hash1\n\n";

// Generate SQL
echo "SQL to update admin password:\n";
echo "─────────────────────────────────────────────────────────\n";
echo "UPDATE users \n";
echo "SET password_hash = '$hash1'\n";
echo "WHERE email = 'admin@tsuniversity.edu.ng';\n";
echo "─────────────────────────────────────────────────────────\n\n";

// Verify the hash works
if (password_verify($password1, $hash1)) {
    echo "✅ Hash verification: SUCCESS\n";
} else {
    echo "❌ Hash verification: FAILED\n";
}

echo "\n═══════════════════════════════════════════════════════════\n";
echo "  Instructions:\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "1. Copy the SQL UPDATE statement above\n";
echo "2. Go to phpMyAdmin on your server\n";
echo "3. Select database: tsuniity_tsu_staff_portal\n";
echo "4. Click 'SQL' tab\n";
echo "5. Paste and run the SQL\n";
echo "6. Try logging in with:\n";
echo "   Email: admin@tsuniversity.edu.ng\n";
echo "   Password: $password1\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Generate a few alternative passwords
echo "Alternative Passwords:\n";
echo "─────────────────────────────────────────────────────────\n";

$alternatives = [
    'password',
    'admin123',
    'tsuadmin2024',
    'Admin@2024'
];

foreach ($alternatives as $pwd) {
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    echo "\nPassword: $pwd\n";
    echo "Hash: $hash\n";
    echo "SQL: UPDATE users SET password_hash = '$hash' WHERE email = 'admin@tsuniversity.edu.ng';\n";
}

echo "\n═══════════════════════════════════════════════════════════\n";
?>
