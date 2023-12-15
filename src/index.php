<?php
// Load the autoloaders, local and composer
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/autoload.php';
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';

// Load the environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/config/site-settings.php';

/*
    Activate Firewall
*/
use \Security\Firewall;

//Firewall::activate();


/*
    Perform login check
*/

use App\RequireLogin;

$loginInfoArray = RequireLogin::check();

/*
Session start
*/

use Core\Session;

Session::start();


/*
    Start Router
*/

use \Core\Router;

$router = new Router($loginInfoArray);

require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/views/routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method, $loginInfoArray);


/*
use \Request\NativeHttp;

$request = new NativeHttp;

$request->get($url);

*/
