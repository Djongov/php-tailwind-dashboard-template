<?php

namespace Core;

use \App\Init;

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

                if ($route['method'] !== 'GET') {
                    return require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/Views/' . $route['controller'];
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

        $this->abort();
    }

    public function abort($code = 404)
    {
        http_response_code($code);
        echo 'Not found';
        //require_once dirname($_SERVER['DOCUMENT_ROOT']) . "templates/errors/$code.php";
    }
}
