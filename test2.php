<?php
require 'app/Core/Database.php';

// I need to test what AdminController is actually doing.
$db = new \App\Core\Database();

// simulate searchUsers logic
$conditions = [];
$conditions[] = "(p.id_card_generated IS NULL OR p.id_card_generated = 0)";

$whereClause = 'WHERE ' . implode(' AND ', $conditions);

$users = $db->fetchAll("
    SELECT u.id, u.email, 
           p.first_name, p.last_name, 
           COALESCE(p.id_card_generated, 0) as id_card_generated
    FROM users u
    LEFT JOIN profiles p ON u.id = p.user_id
    $whereClause
    LIMIT 10
");

print_r($users);
