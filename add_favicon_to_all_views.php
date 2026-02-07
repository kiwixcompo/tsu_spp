<?php
/**
 * Script to add favicon to all view files
 * Run this once: php add_favicon_to_all_views.php
 */

$faviconCode = '    <link rel="icon" type="image/png" href="<?= asset(\'assets/images/tsu-logo.png\') ?>">' . PHP_EOL;

$viewFiles = [
    // Auth views
    'app/Views/auth/login.php',
    'app/Views/auth/register.php',
    'app/Views/auth/verify-email.php',
    'app/Views/auth/forgot-password.php',
    'app/Views/auth/reset-password.php',
    'app/Views/auth/reset-password-expired.php',
    
    // Dashboard
    'app/Views/dashboard/index.php',
    
    // Profile views
    'app/Views/profile/setup.php',
    'app/Views/profile/setup_new.php',
    'app/Views/profile/edit.php',
    'app/Views/profile/education.php',
    'app/Views/profile/experience.php',
    'app/Views/profile/skills.php',
    'app/Views/profile/publications.php',
    
    // Directory views
    'app/Views/directory/index.php',
    'app/Views/directory/profile.php',
    
    // Admin views
    'app/Views/admin/dashboard.php',
    'app/Views/admin/users.php',
    'app/Views/admin/faculties-departments.php',
    'app/Views/admin/publications.php',
    'app/Views/admin/activity-logs.php',
    'app/Views/admin/analytics.php',
    'app/Views/admin/settings.php',
    
    // Settings
    'app/Views/settings/index.php',
    
    // Error pages
    'app/Views/errors/404.php',
    'app/Views/errors/500.php',
    
    // Home
    'app/Views/home/index.php',
    
    // Public files
    'public/simple_homepage.php',
];

$updated = 0;
$skipped = 0;

foreach ($viewFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: $file\n";
        $skipped++;
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Check if favicon already exists
    if (strpos($content, 'tsu-logo.png') !== false && strpos($content, 'favicon') !== false) {
        echo "⏭️  Already has favicon: $file\n";
        $skipped++;
        continue;
    }
    
    // Find the title tag and add favicon after it
    if (preg_match('/<title>.*?<\/title>/s', $content, $matches)) {
        $titleTag = $matches[0];
        $replacement = $titleTag . PHP_EOL . $faviconCode;
        $newContent = str_replace($titleTag, $replacement, $content);
        
        file_put_contents($file, $newContent);
        echo "✅ Updated: $file\n";
        $updated++;
    } else {
        echo "❌ No title tag found: $file\n";
        $skipped++;
    }
}

echo "\n";
echo "═══════════════════════════════════════\n";
echo "Summary:\n";
echo "✅ Updated: $updated files\n";
echo "⏭️  Skipped: $skipped files\n";
echo "═══════════════════════════════════════\n";
echo "\nDone! All view files now have the TSU logo as favicon.\n";
