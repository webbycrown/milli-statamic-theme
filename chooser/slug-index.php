<?php

$slug = basename(__DIR__);
$home = getenv('MILLI_HOME') ?: '/home/webbydemo-milli-statamic';
$prefix = '/'.$slug;
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$query = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_QUERY);
$rest = substr($path, strlen($prefix));
if ($rest === '' || $rest === false) {
    $rest = '/';
}
$_SERVER['REQUEST_URI'] = $rest.($query ? '?'.$query : '');
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$index = $home.'/apps/'.$slug.'/public/index.php';
$_SERVER['SCRIPT_FILENAME'] = $index;
chdir(dirname($index));
require $index;
