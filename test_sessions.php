<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$files = glob(storage_path('framework/sessions/*'));
foreach ($files as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        $data = @unserialize($content);
        if (is_array($data) && isset($data['errors'])) {
            echo "Session file: " . basename($file) . "\n";
            echo "Type of errors: " . gettype($data['errors']) . "\n";
            if (is_object($data['errors'])) {
                echo "Class: " . get_class($data['errors']) . "\n";
            } else {
                var_dump($data['errors']);
            }
        }
    }
}
