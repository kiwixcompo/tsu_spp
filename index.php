<?php
/**
 * Root Redirect to Public Directory
 * This file redirects http://localhost/tsu_spp to http://localhost/tsu_spp/public
 */

// Get the current URL components
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$requestUri = $_SERVER['REQUEST_URI'];

// Build the redirect URL
$redirectUrl = $protocol . '://' . $host . rtrim($requestUri, '/') . '/public/';

// Perform the redirect
header("Location: $redirectUrl", true, 301);
exit;
?>