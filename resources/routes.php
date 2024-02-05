<?php

use FastRoute\RouteCollector;
use Markdown\Page;

function createRoutesMarkDown($folder)
{
    
}

return function (RouteCollector $router) {
    $contollers_folder = dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/controllers';
    $views_folder = dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/views';
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
    /* Views */

    // Landing
        // Root page
        $router->addRoute('GET', '/', [$views_folder . '/landing/main.php', $genericMetaDataArray]);
        // Login page
        $router->addRoute('GET', '/login', [$views_folder . '/landing/login.php', $genericMetaDataArray]);
        // Install apge
        $router->addRoute('GET', '/install', [$views_folder . '/landing/install.php', $genericMetaDataArray]);
        // Register page
        $router->addRoute('GET', '/register', [$views_folder . '/landing/register.php', $genericMetaDataArray]);
        // User settings page
        $router->addRoute('GET', '/user-settings', [$views_folder . '/landing/user-settings.php', $genericMetaDataArray]);

    // Example
    $router->addRoute('GET', '/charts', [$views_folder . '/example/charts.php', $genericMetaDataArray]);
    $router->addRoute('GET', '/forms', [$views_folder . '/example/forms.php', $genericMetaDataArray]);
    $router->addRoute('GET', '/datagrid', [$views_folder . '/example/datagrid.php', $genericMetaDataArray]);
    
    // Auth verify page
    $router->addRoute('POST', '/auth-verify', [$contollers_folder . '/auth-verify.php']);
    // Logout page
    $router->addRoute('GET', '/logout', [$contollers_folder . '/logout.php']);
    // CSP report endpoiont
    $router->addRoute('POST', '/csp-report', [$contollers_folder . '/csp-report.php']);
    // Admin
    $router->addRoute('GET', '/adminx', [$views_folder . '/admin/index.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/server', [$views_folder . '/admin/server.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/users', [$views_folder . '/admin/users.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/cache', [$views_folder . '/admin/cache.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/system-logs', [$views_folder . '/admin/system-logs.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/csp-reports', [$views_folder . '/admin/csp/csp-reports.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/csp-approved-domains', [$views_folder . '/admin/csp/csp-approved-domains.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/firewall', [$views_folder . '/admin/firewall.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/queries', [$views_folder . '/admin/queries.php', $genericMetaAdminDataArray]);

    // Admin API
    $router->addRoute('POST', '/api/admin/csp/add', [$contollers_folder . '/api/admin/csp/add.php']);
    $router->addRoute('POST', '/api/admin/firewall/add', [$contollers_folder . '/api/admin/firewall/add.php']);
    $router->addRoute('POST', '/api/admin/queries', [$contollers_folder . '/api/admin/queries.php']);

    // Tools API
    $router->addRoute('POST', '/api/tools/get-error-file', [$contollers_folder . '/api/tools/get-error-file.php']);
    $router->addRoute('POST', '/api/tools/clear-error-file', [$contollers_folder . '/api/tools/clear-error-file.php']);
    $router->addRoute('POST', '/api/tools/export-csv', [$contollers_folder . '/api/tools/export-csv.php']);
    $router->addRoute('POST', '/api/tools/export-tsv', [$contollers_folder . '/api/tools/export-tsv.php']);

    /* API Routes */
    $router->addRoute(['GET', 'PUT', 'DELETE'], '/api/user/{id:\d+}', [$contollers_folder . '/api/user/index.php']);
    $router->addRoute('POST', '/api/user', [$contollers_folder . '/api/user/index.php']);

    /* DataGrid Api */
    $router->addRoute('POST', '/api/datagrid/get-records', [$contollers_folder . '/api/datagrid/get-records.php']);
    $router->addRoute('POST', '/api/datagrid/update-records', [$contollers_folder . '/api/datagrid/update-records.php']);
    $router->addRoute('POST', '/api/datagrid/delete-records', [$contollers_folder . '/api/datagrid/delete-records.php']);

    // Docs pages markdown auto routing for /docs
    $docsFolder = '/docs';
    $markDownFolder = $views_folder . $docsFolder;
    $router->addRoute('GET', '/docs', [$markDownFolder . '/index.php', Page::getMetaDataFromMd('index', $markDownFolder)]);
    // Search the /docs for files and build a route for each file
    $docFiles = Page::getMdFilesInDir($views_folder . '/docs');
    foreach ($docFiles as $file) {
        $router->addRoute('GET', '/docs/' . $file, [$markDownFolder . '/index.php', Page::getMetaDataFromMd($file, $markDownFolder)]);
    }

    // Test
    $router->addRoute('GET', '/test', [$views_folder . '/test.php', $genericMetaDataArray]);
    // API Example
    $router->addRoute(['PUT', 'DELETE'], '/api/example/{id:\d+}', [$contollers_folder . '/api/example.php']);
    $router->addRoute('POST', '/api/example', [$contollers_folder . '/api/example.php']);
};
