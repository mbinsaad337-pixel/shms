<?php
$dirs = ['app', 'routes', 'resources/views', 'config', 'database'];
$errors = [];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getPathname();
            // run php -l in a clean way
            $cmd = 'php -d display_errors=0 -l ' . escapeshellarg($path) . ' 2>&1';
            exec($cmd, $output, $returnVar);
            $outStr = implode("\n", $output);
            if ($returnVar !== 0) {
                $cleanOut = preg_replace('/PHP Warning:.*openssl.*$/m', '', $outStr);
                $cleanOut = trim($cleanOut);
                $errors[] = "Error in $path:\n$cleanOut";
            }
        }
    }
}
if (empty($errors)) {
    echo "NO SYNTAX ERRORS FOUND.\n";
} else {
    echo "SYNTAX ERRORS FOUND:\n" . implode("\n\n", $errors);
}
