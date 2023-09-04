<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/autoload.php';
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/functions.php';

/*
    Activate Firewall
*/
use \Security\Firewall;

Firewall::activate();


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

require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/Views/routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method, $loginInfoArray);


/*
use \Request\NativeHttp;

$request = new NativeHttp;

$request->get($url);

*/
