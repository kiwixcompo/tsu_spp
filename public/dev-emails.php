<?php
/**
 * Development Email Viewer
 * Shows sent emails and verification codes for local testing
 */

// Only allow in development
if (($_ENV['APP_ENV'] ?? 'production') !== 'development') {
    http_response_code(404);
    exit('Not found');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Development Emails - TSU Staff Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .email-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .email-header {
            background: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .email-body {
            padding: 1rem;
            max-height: 400px;
            overflow-y: auto;
        }
        .verification-code {
            background: #e7f3ff;
            border: 2px solid #0066cc;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #0066cc;
            margin: 1rem 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Development Emails</h1>
                    <div>
                        <a href="/" class="btn btn-outline-primary">Back to Portal</a>
                        <button onclick="location.reload()" class="btn btn-primary">Refresh</button>
                    </div>
                </div>

                <!-- Verification Codes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">🔐 Recent Verification Codes</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $codesFile = __DIR__ . '/verification_codes.txt';
                        if (file_exists($codesFile)) {
                            $codes = array_reverse(array_filter(explode("\n", file_get_contents($codesFile))));
                            if (!empty($codes)) {
                                echo "<div class='row'>";
                                foreach (array_slice($codes, 0, 10) as $codeLine) {
                                    if (preg_match('/^(.+?) \| (.+?) \| (\d{6})$/', trim($codeLine), $matches)) {
                                        echo "<div class='col-md-6 mb-3'>";
                                        echo "<div class='verification-code'>";
                                        echo "<div class='h4 mb-2'>{$matches[3]}</div>";
                                        echo "<small class='text-muted'>{$matches[2]}<br>{$matches[1]}</small>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                }
                                echo "</div>";
                            } else {
                                echo "<p class='text-muted'>No verification codes found.</p>";
                            }
                        } else {
                            echo "<p class='text-muted'>No verification codes file found.</p>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Password Reset Links -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">🔑 Recent Password Reset Links</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $linksFile = __DIR__ . '/reset_links.txt';
                        if (file_exists($linksFile)) {
                            $links = array_reverse(array_filter(explode("\n", file_get_contents($linksFile))));
                            if (!empty($links)) {
                                foreach (array_slice($links, 0, 10) as $linkLine) {
                                    if (preg_match('/^(.+?) \| (.+?) \| (.+)$/', trim($linkLine), $matches)) {
                                        echo "<div class='card mb-2'>";
                                        echo "<div class='card-body py-2'>";
                                        echo "<div class='d-flex justify-content-between align-items-center'>";
                                        echo "<div>";
                                        echo "<strong>{$matches[2]}</strong><br>";
                                        echo "<small class='text-muted'>{$matches[1]}</small>";
                                        echo "</div>";
                                        echo "<div>";
                                        echo "<a href='{$matches[3]}' class='btn btn-sm btn-primary' target='_blank'>Open Reset Link</a>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                }
                            } else {
                                echo "<p class='text-muted'>No reset links found.</p>";
                            }
                        } else {
                            echo "<p class='text-muted'>No reset links file found.</p>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Email Files -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Sent Emails</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $emailDir = __DIR__ . '/../storage/emails';
                        if (is_dir($emailDir)) {
                            $emails = glob($emailDir . '/*.html');
                            rsort($emails); // Most recent first
                            
                            if (!empty($emails)) {
                                foreach (array_slice($emails, 0, 20) as $emailFile) {
                                    $filename = basename($emailFile);
                                    $content = file_get_contents($emailFile);
                                    $timestamp = filemtime($emailFile);
                                    
                                    echo "<div class='email-item'>";
                                    echo "<div class='email-header'>";
                                    echo "<div class='d-flex justify-content-between align-items-center'>";
                                    echo "<h6 class='mb-0'>{$filename}</h6>";
                                    echo "<small class='text-muted'>" . date('Y-m-d H:i:s', $timestamp) . "</small>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "<div class='email-body'>";
                                    echo $content;
                                    echo "</div>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<p class='text-muted'>No emails found.</p>";
                            }
                        } else {
                            echo "<p class='text-muted'>Email storage directory not found.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>