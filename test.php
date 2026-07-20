<?php
$ch = curl_init('http://localhost/tsu_spp/public/admin/users/search');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'data_completeness=ready');
$r = curl_exec($ch);
echo "RESPONSE:\n" . $r;
