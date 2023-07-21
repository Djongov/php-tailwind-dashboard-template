<?php

/* Admin routes */

$router->get('/adminx', '/admin/index.php');

$router->get('/', 'main.php');

$router->get('/users', 'user.php');

$router->get('/user-settings', 'user-settings.php');

$router->post('/user-settings', 'user-settings.php');

$router->post('/auth-verify', 'auth-verify.php');

$router->post('/csp-report', 'csp-report.php');

$router->get('/logout', 'logout.php');

$router->get('/login', 'login.php');

$router->post('/api/get-records', 'get-records.php');

$router->post('/api/update-records', 'update-records.php');

$router->post('/api/delete-records', 'delete-records.php');
