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
                    $title = str_replace('.php', '', $route['uri']);
                    $title = str_replace('/', '', $title);
                    $title = str_replace('-', ' ', $title);
                    $title = ucfirst($title);
                } else {
                    $title = 'Home';
                }
                // Build the page using the App\Init::build()
                $page = new Init;

                echo $page->build($title, $route['controller'], MAIN_MENU, $loginInfoArray);

                return;
            }
        }

        $this->abort($method);
    }

    public function abort($method, $code = 404, $httpErrorMessage = 'Not found')
    {
        http_response_code($code);
        if (strtoupper($method) === 'GET') {
            $page = new Init;
            echo $page->build($httpErrorMessage, "/errors/error.php", null, null);
        } else {
            DieCode::kill('Route not found', $code);
        }
    }
}
