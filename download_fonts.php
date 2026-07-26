<?php
$fontDir = __DIR__ . '/storage/fonts';

$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko\r\n"
    ]
];
$context = stream_context_create($options);

$css = file_get_contents('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700', false, $context);

preg_match_all('/font-weight: (400|700).*?url\((.*?\.ttf)\)/s', $css, $matches);

foreach ($matches[1] as $index => $weight) {
    $url = $matches[2][$index];
    $name = $weight == '700' ? 'cairo-bold.ttf' : 'cairo-regular.ttf';
    
    echo "Downloading $name from $url ...\n";
    $content = file_get_contents($url);
    if ($content) {
        file_put_contents($fontDir . '/' . $name, $content);
        echo "Saved to $fontDir/$name\n";
    }
}
