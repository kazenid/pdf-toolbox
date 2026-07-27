<?php

// Test Ghostscript paths
$paths = [
    'C:\\Program Files\\gs\\gs10.01.2\\bin\\gswin64c.exe',
    'C:\\Program Files\\gs\\gs10.0.0\\bin\\gswin64c.exe',
    'C:\\Program Files (x86)\\gs\\gs10.0.0\\bin\\gswin32c.exe',
    'C:\\Program Files\\gs\\gs9.56.1\\bin\\gswin64c.exe',
    'gswin64c.exe',
    'gswin32c.exe',
];

echo "PHP Version: " . PHP_VERSION . "\n";
echo "System: " . php_uname() . "\n\n";

echo "Testing Ghostscript paths:\n";
echo "================================\n";

foreach ($paths as $path) {
    echo "Path: {$path}\n";
    echo "  File exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
    
    $output = [];
    $returnVar = -1;
    exec("where \"{$path}\" 2>&1", $output, $returnVar);
    echo "  Where command result: " . implode(' | ', $output) . "\n";
    
    // Try to execute
    $testOutput = [];
    $testReturn = -1;
    exec("\"{$path}\" --version 2>&1", $testOutput, $testReturn);
    echo "  Version check return code: {$testReturn}\n";
    if (!empty($testOutput)) {
        echo "  Version output: " . implode('\n  ', $testOutput) . "\n";
    }
    echo "\n";
}

echo "\nEnvironment PATH:\n";
echo "================================\n";
echo getenv('PATH') . "\n\n";

echo "Testing ImageMagick:\n";
echo "================================\n";
$imPaths = [
    'C:\\Program Files\\ImageMagick-7.1.2-Q16-HDRI\\magick.exe',
    'magick.exe',
];

foreach ($imPaths as $path) {
    echo "Path: {$path}\n";
    echo "  File exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
    
    $testOutput = [];
    $testReturn = -1;
    exec("\"{$path}\" --version 2>&1", $testOutput, $testReturn);
    echo "  Version check return code: {$testReturn}\n";
    if (!empty($testOutput)) {
        echo "  First line: " . $testOutput[0] . "\n";
    }
    echo "\n";
}
