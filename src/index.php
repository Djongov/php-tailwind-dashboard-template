<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/autoload.php';

use \Response\Router;
use \App\Init;
use \Security\Firewall;

Firewall::activate();

$router = new Router();

$menuArray = [
    'Users' => [
        'icon' => [
            'type' => 'svg',
            'src' => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <rect x="10" y="9" width="4" height="12" rx="1.105" />
                <rect x="17" y="3" width="4" height="18" rx="1.105" />
                <circle cx="5" cy="19" r="2" />',
        ],
        'link' => '/users/123',
    ]
];

// Add routes
$router->addRoute('GET', '/', function () use($menuArray) {
    $page = new Init;
    echo $page->build('Home', 'main.php', $menuArray, COLOR_SCHEME, 'Demo', true);
});

$router->addRoute('GET', '/users/{id}', function ($params) use ($menuArray) {
    $userId = $params['id'];
    $filePath = 'user.php';
    $page = new Init();
    echo $page->build('User ' . $userId,$filePath, $menuArray, COLOR_SCHEME, 'Demo', true);
});

$router->addRoute('POST', '/csp-report', function () {
    include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/templates/csp-report.php';
});

// Handle the request
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->handleRequest($method, $uri);
