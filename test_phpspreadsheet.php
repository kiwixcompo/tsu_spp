<?php
/**
 * PHPSpreadsheet Installation Test Script
 * 
 * Run this script to verify PHPSpreadsheet is properly installed
 * Usage: php test_phpspreadsheet.php
 */

echo "Testing PHPSpreadsheet Installation...\n\n";

// Check if composer autoload exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "❌ ERROR: Composer autoload not found!\n";
    echo "   Please run: composer install\n\n";
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

// Check if PHPSpreadsheet class exists
if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
    echo "❌ ERROR: PHPSpreadsheet not installed!\n";
    echo "   Please run: composer require phpoffice/phpspreadsheet\n\n";
    exit(1);
}

echo "✓ PHPSpreadsheet is installed!\n\n";

// Try to create a simple spreadsheet
try {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Test');
    $sheet->setCellValue('B1', 'Success');
    
    echo "✓ Can create spreadsheet objects\n";
    echo "✓ Can set cell values\n\n";
    
    // Try to create a writer
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    echo "✓ Can create XLSX writer\n\n";
    
    echo "========================================\n";
    echo "SUCCESS! PHPSpreadsheet is working correctly.\n";
    echo "========================================\n\n";
    
    echo "You can now use the Excel export feature in the admin panel.\n";
    echo "Go to: Admin > Users Management > Export to Excel\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Show version info
try {
    $reflection = new ReflectionClass('\PhpOffice\PhpSpreadsheet\Spreadsheet');
    $filename = $reflection->getFileName();
    echo "Installation Path: " . dirname($filename) . "\n";
} catch (\Exception $e) {
    // Ignore
}

echo "\nTest completed successfully!\n";
