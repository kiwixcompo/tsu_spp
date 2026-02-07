<?php
echo "<h2>PHPMailer Installation Check</h2>";

// Check for Composer autoloader
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
echo "<p><strong>Composer autoload.php:</strong> ";
if (file_exists($composerAutoload)) {
    echo "<span style='color:green;'>✓ EXISTS</span></p>";
    require_once $composerAutoload;
    
    // Test if PHPMailer is available
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        echo "<p><strong>PHPMailer class:</strong> <span style='color:green;'>✓ AVAILABLE</span></p>";
        echo "<p>PHPMailer is properly installed and can be used!</p>";
    } else {
        echo "<p><strong>PHPMailer class:</strong> <span style='color:red;'>✗ NOT AVAILABLE</span></p>";
    }
} else {
    echo "<span style='color:red;'>✗ NOT FOUND</span></p>";
}

// Check PHPMailer files directly
echo "<hr><h3>Direct File Check:</h3>";
$phpmailerFiles = [
    'PHPMailer.php' => __DIR__ . '/../vendor/phpmailer/PHPMailer/src/PHPMailer.php',
    'SMTP.php' => __DIR__ . '/../vendor/phpmailer/PHPMailer/src/SMTP.php',
    'Exception.php' => __DIR__ . '/../vendor/phpmailer/PHPMailer/src/Exception.php',
];

foreach ($phpmailerFiles as $name => $path) {
    echo "<p><strong>$name:</strong> ";
    if (file_exists($path)) {
        echo "<span style='color:green;'>✓ EXISTS</span> ($path)</p>";
    } else {
        echo "<span style='color:red;'>✗ NOT FOUND</span> ($path)</p>";
    }
}

// Try manual loading
echo "<hr><h3>Manual Loading Test:</h3>";
try {
    require_once __DIR__ . '/../vendor/phpmailer/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/PHPMailer/src/SMTP.php';
    
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        echo "<p style='color:green;'><strong>✓ PHPMailer loaded successfully via manual require!</strong></p>";
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        echo "<p style='color:green;'><strong>✓ PHPMailer object created successfully!</strong></p>";
        echo "<p>PHPMailer Version: " . $mail::VERSION . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>✗ Error loading PHPMailer:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr><h3>Recommendation:</h3>";
echo "<p>Add the following code to your public/index.php after the autoloader section:</p>";
echo "<pre style='background:#f5f5f5; padding:15px;'>";
echo htmlspecialchars("
// Load PHPMailer
\$phpmailerPath = __DIR__ . '/../vendor/phpmailer/PHPMailer/src';
if (file_exists(\$phpmailerPath . '/PHPMailer.php')) {
    require_once \$phpmailerPath . '/Exception.php';
    require_once \$phpmailerPath . '/PHPMailer.php';
    require_once \$phpmailerPath . '/SMTP.php';
}
");
echo "</pre>";
?>