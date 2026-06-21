<?php
// Simple cache clearer for Laravel
// Can be accessed publicly via URL: https://vclean.web.id/clear-cache.php?key=vclean123

if (empty($_GET['key']) || $_GET['key'] !== 'vclean123') {
    http_response_code(403);
    die('Forbidden: Access Denied. Secret key required.');
}

// Define route and config caches to remove directly
$files = [
    __DIR__ . '/../bootstrap/cache/routes-v7.php',
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/services.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
];

echo "<h3>Cleaning Laravel Cache...</h3>";

foreach ($files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "✓ Deleted cache file: " . basename($file) . "<br>";
        } else {
            echo "✗ Failed to delete cache file: " . basename($file) . "<br>";
        }
    } else {
        echo "• No cache found for: " . basename($file) . "<br>";
    }
}

// Try using Artisan command programmatically
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<br><h3>Running Artisan Commands...</h3>";
    
    $commands = [
        'route:clear',
        'config:clear',
        'cache:clear',
        'view:clear',
        'storage:link'
    ];
    
    foreach ($commands as $cmd) {
        try {
            $status = \Illuminate\Support\Facades\Artisan::call($cmd);
            $output = \Illuminate\Support\Facades\Artisan::output();
            echo "✓ Command '{$cmd}' executed (status: {$status})<br>";
            if (!empty($output)) {
                echo "<pre>" . htmlspecialchars(trim($output)) . "</pre>";
            }
        } catch (\Exception $e) {
            echo "✗ Command '{$cmd}' failed: " . $e->getMessage() . "<br>";
        }
    }
} catch (\Exception $e) {
    echo "<br>✗ Could not bootstrap Laravel: " . $e->getMessage() . "<br>";
}

echo "<br><b>Cache clear complete! Please try uploading and viewing the profile photos again.</b>";

