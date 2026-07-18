<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$bladeCompiler = app('blade.compiler');
$file = 'resources/views/settlements/index.blade.php';

$content = file_get_contents(__DIR__.'/'.$file);
$compiled = $bladeCompiler->compileString($content);

$tempFile = tempnam(sys_get_temp_dir(), 'blade_') . '.php';
file_put_contents($tempFile, $compiled);

echo "Compiled to: $tempFile\n";
exec("php -l $tempFile", $output, $returnVar);

echo implode("\n", $output);
if ($returnVar !== 0) {
    exit(1);
}
unlink($tempFile);
