<?php

namespace Response;

use \Response\DieCode;
use \Response\HttpErrorHandler;

class Router
{
    private $routes = [];

    public function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function handleRequest($method, $uri)
    {
        $matchedRoute = null;
        $routeParams = [];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri, $routeParams)) {
                $matchedRoute = $route;
                break;
            }
        }

        if ($matchedRoute) {
            $handler = $matchedRoute['handler'];
            if (is_callable($handler)) {
                $handler($routeParams);
            } else {
                echo 'Invalid route handler';
            }
        } else {
            HttpErrorHandler::render(404, 'Page not found', 'Not Found', 'sky');
            http_response_code(404);
        }
    }

    private function matchPath($routePath, $uri, &$routeParams)
    {
        $pattern = preg_replace('~\{([^/]+)\}~', '([^/]+)', $routePath);
        $pattern = '~^' . $pattern . '$~';

        $matches = [];
        $matchCount = preg_match($pattern, $uri, $matches);

        if ($matchCount > 0) {
            array_shift($matches);
            preg_match_all('~\{([^/]+)\}~', $routePath, $paramNames);
            $paramNames = $paramNames[1];
            $paramCount = count($paramNames);
            if ($paramCount > 0) {
                for ($i = 0; $i < $paramCount; $i++) {
                    $routeParams[$paramNames[$i]] = $matches[$i];
                }
            }
            return true;
        }

        return false;
    }
}
