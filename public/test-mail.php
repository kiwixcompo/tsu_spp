<?php
// Simple mail test
$to = "social@tsuniversity.edu.ng"; // REPLACE WITH YOUR ACTUAL EMAIL
$subject = "Test from TSU Portal";
$message = "<html><body><h1>Test Email</h1><p>This is a test email from TSU Staff Portal.</p></body></html>";
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: TSU Staff Portal <noreply@tsuniversity.edu.ng>" . "\r\n";

echo "<h2>Testing PHP mail() function</h2>";
echo "<p><strong>To:</strong> $to</p>";
echo "<p><strong>From:</strong> noreply@tsuniversity.edu.ng</p>";
echo "<p><strong>Subject:</strong> $subject</p>";

$result = mail($to, $subject, $message, $headers);

if ($result) {
    echo "<p style='color: green;'><strong>✓ mail() returned TRUE</strong></p>";
    echo "<p>Check your email inbox (and spam folder) for the test email.</p>";
} else {
    echo "<p style='color: red;'><strong>✗ mail() returned FALSE</strong></p>";
    echo "<p>PHP mail() function failed. Check server configuration.</p>";
}

// Check if verification codes file exists
$codesFile = __DIR__ . '/../public/verification_codes.txt';
if (file_exists($codesFile)) {
    echo "<hr><h3>Recent Verification Codes:</h3>";
    $codes = file_get_contents($codesFile);
    echo "<pre>" . htmlspecialchars($codes) . "</pre>";
} else {
    echo "<hr><p>No verification codes file found.</p>";
}
?>