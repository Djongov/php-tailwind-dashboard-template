<?php

use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $contollers_folder = dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/views';
    // include the menu data
    require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/menus/menus.php';
    $genericMetaDataArray = [
        'metadata' => [
            'title' => 'Home',
            'description' => GENERIC_DESCRIPTION,
            'keywords' => GENERIC_KEYWORDS,
            'thumbimage' => OG_LOGO,
            'menu' => MAIN_MENU,
        ]
    ];
    $genericMetaAdminDataArray = [
        'metadata' => [
            'title' => 'Admin',
            'description' => GENERIC_DESCRIPTION,
            'keywords' => GENERIC_KEYWORDS,
            'thumbimage' => OG_LOGO,
            'menu' => ADMIN_MENU,
        ]
    ];
    // Root page
    $router->addRoute('GET', '/', [$contollers_folder . '/main.php', $genericMetaDataArray]);
    // Login page
    $router->addRoute('GET', '/login', [$contollers_folder . '/login.php', $genericMetaDataArray]);
    // Auth verify page
    $router->addRoute('POST', '/auth-verify', [$contollers_folder . '/auth-verify.php']);
    // Logout page
    $router->addRoute('GET', '/logout', [$contollers_folder . '/logout.php']);
    // User settings page
    $router->addRoute('GET', '/user-settings', [$contollers_folder . '/user-settings.php', $genericMetaDataArray]);
    // Admin page
    $router->addRoute('GET', '/adminx', [$contollers_folder . '/admin/index.php', $genericMetaAdminDataArray]);
    // Admin Server page
    $router->addRoute('GET', '/adminx/server', [$contollers_folder . '/admin/server.php', $genericMetaAdminDataArray]);
    // Admin Server Api
    $router->addRoute('POST', '/api/get-error-file', [$contollers_folder . '/api/get-error-file.php']);
    $router->addRoute('POST', '/api/clear-error-file', [$contollers_folder . '/api/clear-error-file.php']);

    /* API Routes */
    $router->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/api/user/{id:\d+}', [$contollers_folder . '/api/user/index.php']);

    $router->addRoute(['POST'], '/api/user/login', [$contollers_folder . '/api/user/login.php']);

    // Docs pages
    $router->addRoute('GET', '/docs', [$contollers_folder . '/docs/index.php', $genericMetaDataArray]);
    // Search the /docs for files and build a route for each file
    $docFiles = scandir(__DIR__ . '/views/docs');
    $docFiles = array_diff($docFiles, ['.', '..', 'index.php']);
    $docFiles = array_map(function ($file) {
        return str_replace('.md', '', $file);
    }, $docFiles);
    foreach ($docFiles as $file) {
        $router->addRoute('GET', '/docs/' . $file, [$contollers_folder . '/docs/index.php', $genericMetaDataArray]);
    }

    // Api
    $router->addRoute('POST', '/api/example', [$contollers_folder . '/api/example.php']);
};
