<?php

namespace Core;

use \App\Init;
use Response\DieCode;

class Router
{
    protected $routes = [];
    protected $loginInfoArray;

    public function __construct($loginInfoArray)
    {
        $this->loginInfoArray = $loginInfoArray;
    }

    public function add($method, $uri, $controller)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function get($uri, $controller)
    {
        $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        $this->add('POST', $uri, $controller);
    }

    public function delete($uri, $controller)
    {
        $this->add('DELETE', $uri, $controller);
    }
    public static function calculateMenu()
    {
        // Include the menu data
        include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/menus/menus.php';

        $menuArray = MAIN_MENU;
        
        return $menuArray;
    }
    public function route($uri, $method, $loginInfoArray)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {

                // If not GET, just execute controller
                if ($route['method'] !== 'GET') {
                    // Special method to deliver arrays to POST requests
                    Init::passArgs($route['controller'], $loginInfoArray);
                    return;
                }

                if ($route['uri'] !== '/') {
                    // If there is a second /, then it is a subpage, we want to catch the second part
                    if (substr_count($route['uri'], '/') > 1) {
                        $title = substr($route['uri'], strrpos($route['uri'], '/') + 1);
                    } else {
                        $title = $route['uri'];
                    }
                    $title = str_replace('-', ' ', $title);
                    $title = str_replace('/', '', $title);
                    $title = ucfirst($title);
                } else {
                    $title = 'Home';
                }
                // Build the page using the App\Init::build()
                $page = new Init;
                $menuArray = self::calculateMenu();
                echo $page->build($title, $route['controller'], $menuArray, $loginInfoArray);
                return;
            }
        }

        $this->abort($method, $loginInfoArray);
    }
    public function abort($method, $loginInfoArray, $code = 404, $httpErrorMessage = 'Not found')
    {
        http_response_code($code);
        if (strtoupper($method) === 'GET') {
            $page = new Init;
            $menuArray = self::calculateMenu();
            echo $page->build($httpErrorMessage, "/errors/error.php", $menuArray, $loginInfoArray);
        } else {
            DieCode::kill('Route not found', $code);
        }
    }
}
