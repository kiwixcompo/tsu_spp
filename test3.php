<?php
$_POST['id_card_filter'] = 'not_printed';
function sanitizeInput(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
$idCardFilter = sanitizeInput($_POST['id_card_filter'] ?? '');
var_dump($idCardFilter);
var_dump($idCardFilter === 'not_printed');
