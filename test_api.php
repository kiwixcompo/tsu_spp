<?php
$url = 'http://localhost/tsu_spp/public/admin/users/search';
$data = http_build_query([
    'id_card_filter' => 'not_printed',
    'data_completeness' => 'ready',
    'page' => 1
]);
$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => $data
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) {
    echo "Error fetching URL\n";
} else {
    echo "Result:\n" . substr($result, 0, 1000) . "\n";
}
