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
    // Install apge
    $router->addRoute('GET', '/install', [$contollers_folder . '/install.php', $genericMetaDataArray]);
    // Register page
    $router->addRoute('GET', '/register', [$contollers_folder . '/register.php', $genericMetaDataArray]);
    // Auth verify page
    $router->addRoute('POST', '/auth-verify', [$contollers_folder . '/auth-verify.php']);
    // Logout page
    $router->addRoute('GET', '/logout', [$contollers_folder . '/logout.php']);
    // User settings page
    $router->addRoute('GET', '/user-settings', [$contollers_folder . '/user-settings.php', $genericMetaDataArray]);
    // Admin
    $router->addRoute('GET', '/adminx', [$contollers_folder . '/admin/index.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/server', [$contollers_folder . '/admin/server.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/users', [$contollers_folder . '/admin/users.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/cache', [$contollers_folder . '/admin/cache.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/system-logs', [$contollers_folder . '/admin/system-logs.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/csp', [$contollers_folder . '/admin/csp.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/firewall', [$contollers_folder . '/admin/firewall.php', $genericMetaAdminDataArray]);

    // Admin API
    $router->addRoute('POST', '/api/admin/csp/add', [$contollers_folder . '/api/admin/csp/add.php']);
    $router->addRoute('POST', '/api/admin/firewall/add', [$contollers_folder . '/api/admin/firewall/add.php']);

    // Tools API
    $router->addRoute('POST', '/api/tools/get-error-file', [$contollers_folder . '/api/tools/get-error-file.php']);
    $router->addRoute('POST', '/api/tools/clear-error-file', [$contollers_folder . '/api/tools/clear-error-file.php']);
    $router->addRoute('POST', '/api/tools/export-csv', [$contollers_folder . '/api/tools/export-csv.php']);
    $router->addRoute('POST', '/api/tools/export-tsv', [$contollers_folder . '/api/tools/export-tsv.php']);

    /* API Routes */
    $router->addRoute(['GET', 'PUT', 'DELETE'], '/api/user/{id:\d+}', [$contollers_folder . '/api/user/index.php']);
    $router->addRoute('POST', '/api/user', [$contollers_folder . '/api/user/create.php']);
    $router->addRoute('PUT', '/api/user/password-change/{id:\d+}', [$contollers_folder . '/api/user/password-change.php']);

    /* DataGrid Api */
    $router->addRoute('POST', '/api/datagrid/get-records', [$contollers_folder . '/api/datagrid/get-records.php']);
    $router->addRoute('POST', '/api/datagrid/update-records', [$contollers_folder . '/api/datagrid/update-records.php']);
    $router->addRoute('POST', '/api/datagrid/delete-records', [$contollers_folder . '/api/datagrid/delete-records.php']);
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
