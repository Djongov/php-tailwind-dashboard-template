<?php
define("START_TIME", microtime(true));
// Load the autoloaders, local and composer
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/autoload.php';
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';

use App\App;

// Initialize the app
$app = new App();

// Run the app
$app->init();
