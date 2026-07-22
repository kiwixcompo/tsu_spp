<?php
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE users (id INTEGER, name TEXT)");
$pdo->exec("INSERT INTO users VALUES (1, 'Alice'), (2, 'Bob'), (3, 'Charlie')");

$params = ['Alice', 1];
$sql = "SELECT * FROM users WHERE name = ? LIMIT ?";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $paramName = is_int($key) ? $key + 1 : $key;
    echo "Binding $paramName with value $value and type $type\n";
    $stmt->bindValue($paramName, $value, $type);
}
$stmt->execute();
print_r($stmt->fetchAll());
