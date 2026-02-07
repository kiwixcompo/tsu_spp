<?php
/**
 * Deployment Test Script
 * Upload this to your public folder and visit it to check server configuration
 * DELETE THIS FILE after successful deployment for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>TSU Staff Portal - Deployment Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; }
        h1 { color: #1e40af; }
        .section { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
    <h1>🚀 TSU Staff Portal - Deployment Check</h1>
    <p><strong>Server:</strong> <?= $_SERVER['HTTP_HOST'] ?></p>
    <p><strong>Time:</strong> <?= date('Y-m-d H:i:s') ?></p>

    <div class="section">
        <h2>1. PHP Configuration</h2>
        <table>
            <tr>
                <td>PHP Version</td>
                <td class="status <?= version_compare(PHP_VERSION, '7.4.0') >= 0 ? 'success' : 'error' ?>">
                    <?= PHP_VERSION ?> <?= version_compare(PHP_VERSION, '7.4.0') >= 0 ? '✅' : '❌ Need 7.4+' ?>
                </td>
            </tr>
            <?php
            $required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl', 'curl'];
            foreach ($required_extensions as $ext):
                $loaded = extension_loaded($ext);
            ?>
            <tr>
                <td><?= $ext ?></td>
                <td class="status <?= $loaded ? 'success' : 'error' ?>">
                    <?= $loaded ? '✅ Loaded' : '❌ Missing' ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>2. File System</h2>
        <table>
            <?php
            $paths = [
                'Current Directory' => __DIR__,
                'Parent Directory' => dirname(__DIR__),
            ];
            foreach ($paths as $label => $path):
            ?>
            <tr>
                <td><?= $label ?></td>
                <td><?= $path ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>3. Directory Permissions</h2>
        <table>
            <?php
            $dirs_to_check = [
                '../storage' => 'Storage directory',
                '../public/uploads' => 'Uploads directory',
                '../app' => 'App directory',
            ];
            
            foreach ($dirs_to_check as $dir => $label):
                $exists = is_dir($dir);
                $writable = $exists && is_writable($dir);
            ?>
            <tr>
                <td><?= $label ?></td>
                <td class="status <?= $writable ? 'success' : ($exists ? 'warning' : 'error') ?>">
                    <?php if (!$exists): ?>
                        ❌ Does not exist
                    <?php elseif ($writable): ?>
                        ✅ Writable
                    <?php else: ?>
                        ⚠️ Not writable (chmod 755 needed)
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>4. Required Files</h2>
        <table>
            <?php
            $files_to_check = [
                '.htaccess' => '.htaccess file',
                'index.php' => 'Index file',
                '../app/Core/Router.php' => 'Router',
                '../app/Core/Database.php' => 'Database class',
                '../app/Helpers/UrlHelper.php' => 'URL Helper',
            ];
            
            foreach ($files_to_check as $file => $label):
                $exists = file_exists($file);
            ?>
            <tr>
                <td><?= $label ?></td>
                <td class="status <?= $exists ? 'success' : 'error' ?>">
                    <?= $exists ? '✅ Found' : '❌ Missing' ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>5. Database Connection</h2>
        <?php
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=tsuniity_tsu_staff_portal',
                'tsuniity',
                '?8I;#ETBGvrU',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            echo '<p class="success">✅ Database connection successful!</p>';
            
            // Check tables
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo '<p><strong>Tables found:</strong> ' . count($tables) . '</p>';
            
            if (count($tables) > 0) {
                echo '<ul>';
                foreach ($tables as $table) {
                    echo '<li>' . htmlspecialchars($table) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="warning">⚠️ No tables found. Run database migrations!</p>';
            }
            
            // Check for admin user
            $adminExists = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'admin@tsuniversity.edu.ng'")->fetchColumn();
            echo '<p class="' . ($adminExists ? 'success' : 'warning') . '">';
            echo $adminExists ? '✅ Admin user exists' : '⚠️ Admin user not found';
            echo '</p>';
            
        } catch (PDOException $e) {
            echo '<p class="error">❌ Database connection failed!</p>';
            echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>6. URL Helper Test</h2>
        <?php
        if (file_exists('../app/Helpers/UrlHelper.php')) {
            require_once '../app/Helpers/UrlHelper.php';
            echo '<p><strong>Base URL:</strong> ' . getBaseUrl() . '</p>';
            echo '<p><strong>Test URL:</strong> ' . url('login') . '</p>';
            echo '<p class="success">✅ URL Helper working</p>';
        } else {
            echo '<p class="error">❌ URL Helper not found</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>7. Apache Modules</h2>
        <?php if (function_exists('apache_get_modules')): ?>
            <?php
            $modules = apache_get_modules();
            $required_modules = ['mod_rewrite', 'mod_headers'];
            ?>
            <table>
                <?php foreach ($required_modules as $mod): ?>
                <tr>
                    <td><?= $mod ?></td>
                    <td class="status <?= in_array($mod, $modules) ? 'success' : 'error' ?>">
                        <?= in_array($mod, $modules) ? '✅ Enabled' : '❌ Disabled' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="warning">⚠️ Cannot check Apache modules (function not available)</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>8. Next Steps</h2>
        <?php
        $all_good = true;
        
        // Check critical items
        if (version_compare(PHP_VERSION, '7.4.0') < 0) $all_good = false;
        if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) $all_good = false;
        if (!file_exists('.htaccess')) $all_good = false;
        
        if ($all_good):
        ?>
            <p class="success">✅ All critical checks passed!</p>
            <ol>
                <li>Visit the homepage: <a href="/">Go to Homepage</a></li>
                <li>Test registration and login</li>
                <li><strong>DELETE THIS FILE</strong> for security!</li>
            </ol>
        <?php else: ?>
            <p class="error">❌ Some critical checks failed. Fix the issues above before proceeding.</p>
        <?php endif; ?>
    </div>

    <hr>
    <p><small><strong>⚠️ SECURITY WARNING:</strong> Delete this file after successful deployment!</small></p>
</body>
</html>
