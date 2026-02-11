<?php
/**
 * Sync Faculties and Departments to Production Database
 * Run this once to update the faculties_departments table
 * Access: https://staff.tsuniversity.ng/sync_faculties.php
 * 
 * IMPORTANT: Delete this file after running!
 */

// Load environment variables
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (strlen($value) > 0) {
                if (($value[0] === '"' && substr($value, -1) === '"') || 
                    ($value[0] === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            $_ENV[$name] = $value;
        }
    }
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_DATABASE'] ?? '';
$username = $_ENV['DB_USERNAME'] ?? '';
$password = $_ENV['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<!DOCTYPE html><html><head><title>Sync Faculties</title>";
    echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}";
    echo ".success{color:green;background:#d4edda;padding:10px;border-radius:5px;margin:5px 0;}";
    echo ".error{color:red;background:#f8d7da;padding:10px;border-radius:5px;margin:5px 0;}";
    echo ".info{color:#004085;background:#cce5ff;padding:10px;border-radius:5px;margin:5px 0;}";
    echo ".warning{color:#856404;background:#fff3cd;padding:15px;border-radius:5px;margin:10px 0;}";
    echo "</style></head><body>";
    
    echo "<h1>🔄 Syncing Faculties and Departments</h1>";
    
    // Clear existing data
    echo "<div class='info'>Clearing existing data...</div>";
    $pdo->exec("DELETE FROM faculties_departments");
    echo "<div class='success'>✓ Existing data cleared</div>";
    
    // Complete faculty-department data
    $data = [
        'Faculty of Agriculture' => [
            'Agronomy',
            'Animal Science',
            'Crop Production',
            'Forestry & Wildlife Conservation',
            'Home Economics',
            'Soil Science & Land Resources Management'
        ],
        'Faculty of Arts' => [
            'English & Literary Studies',
            'Theatre & Film Studies',
            'French',
            'History',
            'Arabic Studies',
            'Languages & Linguistics'
        ],
        'Faculty of Communication & Media' => [
            'Mass Communication'
        ],
        'Faculty of Computing & Artificial Intelligence' => [
            'Computer Science',
            'Data Science and Artificial Intelligence',
            'Information and Communication Technology',
            'Software Engineering'
        ],
        'Faculty of Education' => [
            'Arts Education',
            'Educational Foundations',
            'Counselling, Educational Psychology and Human Development',
            'Science Education',
            'Human Kinetics & Physical Education',
            'Social Science Education',
            'Vocational & Technology Education',
            'Library & Information Science'
        ],
        'Faculty of Engineering' => [
            'Agric & Bio-Resources Engineering',
            'Electrical/Electronics Engineering',
            'Civil Engineering',
            'Mechanical Engineering'
        ],
        'Faculty of Health Sciences' => [
            'Environmental Health',
            'Public Health',
            'Nursing',
            'Medical Laboratory Science'
        ],
        'Faculty of Law' => [
            'Public Law',
            'Private & Property Law'
        ],
        'Faculty of Management Sciences' => [
            'Accounting',
            'Business Administration',
            'Public Administration',
            'Hospitality and Tourism Management'
        ],
        'Faculty of Religion & Philosophy' => [
            'Islamic Studies',
            'CRS'
        ],
        'Faculty of Science' => [
            'Biological Sciences',
            'Chemical Sciences',
            'Mathematics and Statistics',
            'Physics'
        ],
        'Faculty of Social Sciences' => [
            'Economics',
            'Geography',
            'Political & International Relations',
            'Peace & Conflict Studies',
            'Sociology'
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO faculties_departments (faculty, department) VALUES (?, ?)");
    $totalCount = 0;
    
    foreach ($data as $faculty => $departments) {
        echo "<div class='info'>Adding <strong>$faculty</strong>...</div>";
        foreach ($departments as $department) {
            $stmt->execute([$faculty, $department]);
            $totalCount++;
        }
        echo "<div class='success'>✓ Added " . count($departments) . " departments</div>";
    }
    
    echo "<div class='success'>";
    echo "<h2>✓ Sync Complete!</h2>";
    echo "<p><strong>Total records inserted:</strong> $totalCount</p>";
    echo "<p><strong>Total faculties:</strong> " . count($data) . "</p>";
    echo "</div>";
    
    // Verify the data
    echo "<h2>Verification</h2>";
    $faculties = $pdo->query("SELECT DISTINCT faculty FROM faculties_departments ORDER BY faculty")->fetchAll(PDO::FETCH_COLUMN);
    echo "<div class='info'>";
    echo "<p><strong>Faculties in database:</strong></p><ul>";
    foreach ($faculties as $faculty) {
        echo "<li>$faculty</li>";
    }
    echo "</ul></div>";
    
    echo "<div class='warning'>";
    echo "<h3>⚠️ IMPORTANT</h3>";
    echo "<p><strong>Delete this file (sync_faculties.php) now for security!</strong></p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";
