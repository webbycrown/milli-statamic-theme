<?php

$home = getenv('MILLI_HOME') ?: '/home/webbydemo-milli-statamic';
$apps = [
    'original',
    'fashion',
    'menswear',
    'womenswear',
    'kids',
    'streetwear',
    'jewellery',
    'beauty',
    'accessories',
    'homeware',
];
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';
$query = parse_url($uri, PHP_URL_QUERY);

foreach ($apps as $slug) {
    $prefix = '/'.$slug;
    $matched = $path === $prefix || $path === $prefix.'/' || str_starts_with($path, $prefix.'/');
    if (! $matched) {
        continue;
    }
    $rest = substr($path, strlen($prefix));
    if ($rest === '' || $rest === false) {
        $rest = '/';
    }

    $publicRoot = $home.'/apps/'.$slug.'/public';
    $static = realpath($publicRoot.$rest);
    if ($static && str_starts_with($static, realpath($publicRoot)) && is_file($static)) {
        $ext = strtolower(pathinfo($static, PATHINFO_EXTENSION));
        $types = [
            'html' => 'text/html; charset=UTF-8',
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'zip' => 'application/zip',
            'woff2' => 'font/woff2',
            'woff' => 'font/woff',
            'ico' => 'image/x-icon',
        ];
        if (isset($types[$ext])) {
            header('Content-Type: '.$types[$ext]);
        }
        header('Content-Length: '.filesize($static));
        readfile($static);
        exit;
    }

    $_SERVER['REQUEST_URI'] = $rest.($query ? '?'.$query : '');
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $index = $publicRoot.'/index.php';
    $_SERVER['SCRIPT_FILENAME'] = $index;
    chdir(dirname($index));
    require $index;
    exit;
}

if ($path === '/' || $path === '/index.html' || $path === '/index.php') {
    header('Content-Type: text/html; charset=UTF-8');
    readfile(__DIR__.'/chooser.html');
    exit;
}

$hubFile = realpath(__DIR__.$path);
if ($hubFile && str_starts_with($hubFile, realpath(__DIR__)) && is_file($hubFile)) {
    $ext = strtolower(pathinfo($hubFile, PATHINFO_EXTENSION));
    $types = [
        'html' => 'text/html; charset=UTF-8',
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'zip' => 'application/zip',
    ];
    if (isset($types[$ext])) {
        header('Content-Type: '.$types[$ext]);
    }
    header('Content-Length: '.filesize($hubFile));
    readfile($hubFile);
    exit;
}

http_response_code(404);
header('Content-Type: text/html; charset=UTF-8');
echo '<!doctype html><title>Not found</title><p>That page is not on this demo. <a href="/">Back to the catalogues</a>.</p>';
