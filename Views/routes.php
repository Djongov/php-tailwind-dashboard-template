<?php

/* Admin routes */

$router->get('/adminx', '/admin/index.php');

/* Test routes */

$router->post('/api/example', '/test/example.php');

/* Main routes */

$router->get('/', 'main.php');

$router->get('/users', 'user.php');

$router->get('/user-settings', 'user-settings.php');

$router->post('/user-settings', 'user-settings.php');

$router->post('/auth-verify', 'auth-verify.php');

$router->post('/csp-report', 'csp-report.php');

$router->get('/logout', 'logout.php');

$router->get('/login', 'login.php');

/* Api Routes */

$router->post('/api/export-tsv', '/api/export-tsv.php');

$router->post('/api/export-csv', '/api/export-csv.php');

$router->get('/api/export-tsv', '/api/export-tsv.php');

$router->get('/api/export-csv', '/api/export-csv.php');

$router->post('/api/get-records', '/api/get-records.php');

$router->post('/api/update-records', '/api/update-records.php');

$router->post('/api/delete-records', '/api/delete-records.php');
