<!DOCTYPE html>
<html>
<head>
    <title>Admin Credentials</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1e40af;
            margin-bottom: 20px;
        }
        .credential {
            background: #f0f9ff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #1e40af;
        }
        .label {
            font-weight: bold;
            color: #666;
            font-size: 14px;
        }
        .value {
            font-size: 18px;
            color: #1e40af;
            font-family: monospace;
            margin-top: 5px;
        }
        .note {
            background: #fff3cd;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔐 Admin Credentials</h1>
        
        <div class="credential">
            <div class="label">Email:</div>
            <div class="value">admin@tsuniversity.edu.ng</div>
        </div>
        
        <div class="credential">
            <div class="label">Password:</div>
            <div class="value">Admin123!</div>
        </div>
        
        <div class="note">
            <strong>⚠️ Note:</strong> If the password doesn't work, you need to:
            <ol>
                <li>Start your MySQL/MariaDB server (WAMP/XAMPP)</li>
                <li>Run <code>php fix_admin_password.php</code> to reset it</li>
            </ol>
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="<?= isset($_SERVER['REQUEST_URI']) ? '/tsu_spp/public/login' : 'public/login' ?>" 
               style="display: inline-block; padding: 12px 24px; background: #1e40af; color: white; text-decoration: none; border-radius: 5px;">
                Go to Login
            </a>
        </div>
    </div>
</body>
</html>
