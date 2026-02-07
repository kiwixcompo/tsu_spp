<?php
/**
 * URL Diagnostic and Fix Script
 * Upload this to your public folder and run it to see what's happening
 * DELETE after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>URL Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; }
        h1 { color: #1e40af; }
        .section { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .good { color: #10b981; font-weight: bold; }
        .bad { color: #ef4444; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 URL Diagnostic Tool</h1>
    <p><strong>Server:</strong> <?= $_SERVER['HTTP_HOST'] ?></p>

    <div class="section">
        <h2>1. Server Variables</h2>
        <table>
            <tr>
                <td><strong>HTTP_HOST</strong></td>
                <td class="code"><?= $_SERVER['HTTP_HOST'] ?? 'NOT SET' ?></td>
            </tr>
            <tr>
                <td><strong>SCRIPT_NAME</strong></td>
                <td class="code"><?= $_SERVER['SCRIPT_NAME'] ?? 'NOT SET' ?></td>
            </tr>
            <tr>
                <td><strong>SCRIPT_FILENAME</strong></td>
                <td class="code"><?= $_SERVER['SCRIPT_FILENAME'] ?? 'NOT SET' ?></td>
            </tr>
            <tr>
                <td><strong>DOCUMENT_ROOT</strong></td>
                <td class="code"><?= $_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET' ?></td>
            </tr>
            <tr>
                <td><strong>REQUEST_URI</strong></td>
                <td class="code"><?= $_SERVER['REQUEST_URI'] ?? 'NOT SET' ?></td>
            </tr>
            <tr>
                <td><strong>PHP_SELF</strong></td>
                <td class="code"><?= $_SERVER['PHP_SELF'] ?? 'NOT SET' ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>2. Path Analysis</h2>
        <?php
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dirname = dirname($scriptName);
        $basePath = str_replace('/index.php', '', $dirname);
        $basePath = rtrim($basePath, '/');
        ?>
        <table>
            <tr>
                <td><strong>Script Name</strong></td>
                <td class="code"><?= $scriptName ?></td>
            </tr>
            <tr>
                <td><strong>Dirname</strong></td>
                <td class="code"><?= $dirname ?></td>
            </tr>
            <tr>
                <td><strong>Calculated Base Path</strong></td>
                <td class="code"><?= $basePath ?: '/' ?></td>
            </tr>
            <tr>
                <td><strong>Contains "tsu_spp"?</strong></td>
                <td class="<?= strpos($basePath, 'tsu_spp') !== false ? 'bad' : 'good' ?>">
                    <?= strpos($basePath, 'tsu_spp') !== false ? '❌ YES (This is the problem!)' : '✅ NO (Good!)' ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>3. Current URL Helper Output</h2>
        <?php
        if (file_exists('../app/Helpers/UrlHelper.php')) {
            require_once '../app/Helpers/UrlHelper.php';
            $baseUrl = getBaseUrl();
            $testUrl = url('login');
            ?>
            <table>
                <tr>
                    <td><strong>getBaseUrl()</strong></td>
                    <td class="code"><?= $baseUrl ?></td>
                </tr>
                <tr>
                    <td><strong>url('login')</strong></td>
                    <td class="code"><?= $testUrl ?></td>
                </tr>
                <tr>
                    <td><strong>Contains "tsu_spp"?</strong></td>
                    <td class="<?= strpos($testUrl, 'tsu_spp') !== false ? 'bad' : 'good' ?>">
                        <?= strpos($testUrl, 'tsu_spp') !== false ? '❌ YES (Problem!)' : '✅ NO (Good!)' ?>
                    </td>
                </tr>
            </table>
        <?php } else { ?>
            <p class="bad">❌ UrlHelper.php not found!</p>
        <?php } ?>
    </div>

    <div class="section">
        <h2>4. Problem Diagnosis</h2>
        <?php
        $hasProblem = strpos($basePath, 'tsu_spp') !== false;
        
        if ($hasProblem) {
            echo '<p class="bad">❌ <strong>PROBLEM FOUND:</strong> Your server path includes "tsu_spp"</p>';
            echo '<p>This means your files are uploaded to a folder named "tsu_spp" on the server.</p>';
            
            echo '<h3>Solutions:</h3>';
            echo '<ol>';
            echo '<li><strong>Option A (Recommended):</strong> Move files out of tsu_spp folder to root</li>';
            echo '<li><strong>Option B:</strong> Update UrlHelper to strip "tsu_spp" from path</li>';
            echo '<li><strong>Option C:</strong> Set document root to point directly to public folder</li>';
            echo '</ol>';
            
            echo '<h3>Quick Fix Code:</h3>';
            echo '<p>Add this to UrlHelper.php in the production section:</p>';
            echo '<pre>';
            echo htmlspecialchars('// Remove tsu_spp from path if present
$basePath = str_replace(\'/tsu_spp\', \'\', $basePath);
$basePath = str_replace(\'/public\', \'\', $basePath);');
            echo '</pre>';
            
        } else {
            echo '<p class="good">✅ <strong>NO PROBLEM:</strong> Path looks clean!</p>';
            echo '<p>URLs should be working correctly.</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>5. Recommended Fix</h2>
        <p>Based on the diagnosis above, here's what you should do:</p>
        
        <?php if ($hasProblem): ?>
            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">
                <h3>Apply This Fix:</h3>
                <p>Update your <code>app/Helpers/UrlHelper.php</code> file.</p>
                <p>In the production section, add these lines after calculating $basePath:</p>
                <pre><?php echo htmlspecialchars('// Remove tsu_spp and public from path
$basePath = str_replace(\'/tsu_spp\', \'\', $basePath);
$basePath = str_replace(\'/public\', \'\', $basePath);
$basePath = rtrim($basePath, \'/\');'); ?></pre>
            </div>
        <?php else: ?>
            <div style="background: #d1fae5; padding: 15px; border-radius: 5px; border-left: 4px solid #10b981;">
                <p class="good">✅ Everything looks good! URLs should be working correctly.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>6. Test Links</h2>
        <p>Click these links to test if URLs are working:</p>
        <ul>
            <li><a href="<?= url('') ?>">Homepage</a></li>
            <li><a href="<?= url('login') ?>">Login</a></li>
            <li><a href="<?= url('register') ?>">Register</a></li>
            <li><a href="<?= url('directory') ?>">Directory</a></li>
        </ul>
    </div>

    <hr>
    <p><strong>⚠️ DELETE THIS FILE after fixing the issue!</strong></p>
</body>
</html>
