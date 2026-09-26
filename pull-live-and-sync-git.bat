@echo off
setlocal enabledelayedexpansion
title TSU Staff Portal - Pull Live & Sync Git
color 0B

echo =======================================================
echo   TSU STAFF PORTAL - LIVE PULL ^& GIT SYNC
echo   Target: https://staff.tsuniversity.ng -^> Local -^> Git
echo =======================================================
echo.

where php >nul 2>nul
if %ERRORLEVEL% neq 0 (
    color 0C
    echo [ERROR] PHP is not installed or not in your PATH.
    pause
    exit /b 1
)

where git >nul 2>nul
if %ERRORLEVEL% neq 0 (
    color 0C
    echo [ERROR] Git is not installed or not in your PATH.
    pause
    exit /b 1
)

echo [*] Triggering Pull and Sync via AppSync Hub engine...
php -r "
require 'C:/wamp64/www/app_sync_hub/src/DiffEngine.php';
require 'C:/wamp64/www/app_sync_hub/src/MalwareScanner.php';

\$_POST = [
    'action' => 'pull_and_sync_git',
    'app_id' => 'tsu_spp',
    'auto_push' => '1',
    'commit_message' => 'Sync live updates from cPanel (' . date('Y-m-d H:i:s') . ')'
];

ob_start();
include 'C:/wamp64/www/app_sync_hub/src/api.php';
\$output = ob_get_clean();
\$res = json_decode(\$output, true);

if (\$res && !empty(\$res['logs'])) {
    foreach (\$res['logs'] as \$l) echo \$l . PHP_EOL;
}
if (\$res && !empty(\$res['success'])) {
    echo PHP_EOL . 'SUCCESS: ' . \$res['message'] . PHP_EOL;
} else {
    echo PHP_EOL . 'ERROR: ' . (\$res['error'] ?? \$output) . PHP_EOL;
    exit(1);
}
"

if %ERRORLEVEL% neq 0 (
    color 0C
    echo.
    echo [ERROR] Synchronization encountered an issue.
    pause
    exit /b 1
)

echo.
color 0A
echo =======================================================
echo   PULL ^& GIT SYNC COMPLETED SUCCESSFULLY!
echo =======================================================
echo.
pause
