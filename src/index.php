<?php
define("START_TIME", microtime(true));
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
use Components\Page;
use Api\Output;

/*
Session start
*/

use Core\Session;

Session::start();

/*
    Start Router


use \Core\Router;

$router = new Router($loginInfoArray);

require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/views/routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method, $loginInfoArray);

*/
$routesDefinition = require dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/routes.php';

// Ensure that $routesDefinition is a callable
if (!is_callable($routesDefinition)) {
    throw new RuntimeException('Invalid routes definition');
}

$dispatcher = FastRoute\simpleDispatcher($routesDefinition);

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if ($pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // Handle 404 Not Found
        if ($httpMethod === 'GET') {
            $loginInfoArray = RequireLogin::check();
            var_dump($loginInfoArray);
            echo '404 Not Found';
        } else {
            // For non-GET requests, provide an API response
            Output::error('api endpoint (' . $uri . ') not found', 404);
        }
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // Handle 405 Method Not Allowed
        echo '405 Method Not Allowed';
        break;

    case FastRoute\Dispatcher::FOUND:
        $controllerName = $routeInfo[1][0]; // the controller
        // Now let's capture all the parameters $routeInfo[1][1]['metadata']
        $params = isset($routeInfo[1][1]['metadata']) ? $routeInfo[1][1]['metadata'] : [];
        // Include and execute the PHP file
        if (file_exists($controllerName)) {

            /* Do login check */
            $loginInfoArray = RequireLogin::check();
            // Assign some variables to pass on to the view for general use
            $usernameArray = $loginInfoArray['usernameArray'];
            $vars['usernameArray'] = $usernameArray;
            $loggedIn = $loginInfoArray['loggedIn'];
            $vars['loggedIn'] = $loggedIn;
            $isAdmin = $loginInfoArray['isAdmin'];
            $vars['isAdmin'] = $isAdmin;
            $theme = (isset($usernameArray['theme'])) ? $usernameArray['theme'] : COLOR_SCHEME;
            $vars['theme'] = $theme;
            
            if ($httpMethod === 'GET') {
                if (!empty($params)) {
                    $menuArray = $params['menu'];
                    echo Page::head($params['title'], $params['description'], $params['keywords'], $params['thumbimage'], $theme);
                    echo '<body class="h-full antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-400">';
                        echo '<div class="md:mx-auto bg-gray-200 dark:bg-gray-800">';
                            echo Page::header($usernameArray, $menuArray, $isAdmin, $theme);
                            include $controllerName;
                        echo Page::footer($theme);
                    echo '</div>';
                    echo '</body>';
                } else {
                    include $controllerName;
                }
            } else {
                include $controllerName;
            }
        } else {
            throw new Exception('Controller file (' . $controllerName . ') not found');
        }
        break;
}
