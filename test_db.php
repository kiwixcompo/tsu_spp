<?php
require 'public/index.php';
try {
    $db = App\Core\Database::getInstance();
    $db->fetchAll("SELECT directorate FROM profiles LIMIT 1");
    echo "SUCCESS: directorate exists";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
