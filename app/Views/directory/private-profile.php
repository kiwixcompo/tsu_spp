<?php if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
if (!function_exists('escape_html')) {
    require_once __DIR__ . '/../../Helpers/TextHelper.php';
} ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Private Profile - TSU Staff Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .private-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 90%;
            padding: 50px 40px;
            text-align: center;
        }
        
        .icon-wrapper {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .icon-wrapper i {
            font-size: 60px;
            color: white;
        }
        
        h1 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .staff-name {
            color: #667eea;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .staff-info {
            color: #718096;
            font-size: 16px;
            margin-bottom: 25px;
        }
        
        .message {
            color: #4a5568;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            padding: 20px;
            background: #f7fafc;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
        }
        
        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #48bb78;
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="private-card">
        <div class="icon-wrapper">
            <i class="fas fa-user-lock"></i>
        </div>
        
        <div class="verified-badge">
            <i class="fas fa-check-circle"></i>
            Verified TSU Staff Member
        </div>
        
        <h1>Private Profile</h1>
        
        <div class="staff-name">
            <?= htmlspecialchars($profile['title'] ?? '') ?> 
            <?= htmlspecialchars($profile['first_name']) ?> 
            <?= htmlspecialchars($profile['last_name']) ?>
        </div>
        
        <div class="staff-info">
            <?php if (!empty($profile['designation'])): ?>
                <div><strong><?= htmlspecialchars($profile['designation']) ?></strong></div>
            <?php endif; ?>
            <?php if (!empty($profile['staff_number'])): ?>
                <div>Staff Number: <?= htmlspecialchars($profile['staff_number']) ?></div>
            <?php endif; ?>
        </div>
        
        <div class="message">
            <i class="fas fa-info-circle" style="color: #667eea; margin-right: 8px;"></i>
            This staff member is a verified employee of Taraba State University but has chosen to keep their profile private.
        </div>
        
        <?php
        // Determine back URL based on user role
        $backUrl = url('directory');
        if (isset($_SESSION['user'])) {
            $userRole = $_SESSION['user']['role'] ?? 'user';
            if ($userRole === 'id_card_manager') {
                $backUrl = url('id-card-manager/dashboard');
            } elseif ($userRole === 'admin') {
                $backUrl = url('admin/dashboard');
            } elseif ($userRole === 'user') {
                $backUrl = url('dashboard');
            }
        }
        ?>
        
        <a href="<?= $backUrl ?>" class="btn-home">
            <i class="fas fa-arrow-left me-2"></i>Go Back
        </a>
    </div>
</body>
</html>
