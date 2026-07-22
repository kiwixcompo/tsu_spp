<?php
require 'config/database.php';
$config = require 'config/database.php';
$c = $config['connections']['mysql'];
$dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['database']}";

try {
    $pdo = new PDO($dsn, $c['username'], $c['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
    ]);
    
    $stmt = $pdo->prepare("SELECT 1 LIMIT ?");
    $stmt->execute([10]);
    echo "SUCCESS WITH INT\n";

    $stmt->execute(['10']);
    echo "SUCCESS WITH STRING\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
