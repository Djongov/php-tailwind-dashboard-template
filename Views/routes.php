<?php

$router->get('/', 'main.php');

$router->get('/users', 'user.php');

$router->get('/user-settings', 'user-settings.php');

$router->post('/user-settings', 'user-settings.php');

$router->post('/auth-verify', 'auth-verify.php');

$router->post('/csp-report', 'csp-report.php');

$router->get('/logout', 'logout.php');

$router->get('/login', 'login.php');