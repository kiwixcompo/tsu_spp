<?php
require 'vendor/autoload.php';

// Load .env
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

require 'app/Core/Database.php';

$db = new \App\Core\Database();
print_r($db->fetchAll('DESCRIBE profiles'));
