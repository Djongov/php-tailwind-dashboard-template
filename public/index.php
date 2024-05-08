<?php
define("START_TIME", microtime(true));
// autoloader
$path = dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if (file_exists($path)) {
    require_once $path;
    // Additional code that depends on the autoload.php file
} else {
    die('<b>' . $path . '</b> file not found. You need to run <b>composer update</b>');
}

function dd()
{
    array_map(function ($x) {
        var_dump($x);
    }, func_get_args());
    die;
}

use App\App;

// Initialize the app
$app = new App();

// Run the app
//if ($_SERVER['REQUEST_URI'] !== '/create-env') {
    $app->init();
//}
