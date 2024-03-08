<?php

use Controllers\Api\Output;
use Controllers\Api\Checks;
use Controllers\Api\Firewall;

// This is the API endpoint controller for the user actions

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // This endpoint is for creating a new local user. Cloud users are create in /auth-verify
    $checks = new Checks($vars, $_GET);
    $checks->apiChecksNoCSRF();
    // check if cidr has been passed
    if (!isset($_GET['cidr'])) {
        $ip = '';
    } else {
        $ip = $_GET['cidr'];
    }

    $firewall = new Firewall();

    $firewall->get($ip);
}

// POST /api/user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checks = new Checks($vars, $_POST);
    $checks->apiChecks();

    $checks->checkParams(['cidr'], $_POST);

    $comment = $_POST['comment'] ?? '';

    $ip = $_POST['cidr'];

    $save = new Firewall();

    $save->add($ip, $comment);
}


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Let's catch php input stream
    $data = Checks::jsonBody();

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        Output::error('Missing id', 400);
        exit();
    }

    $update = new Firewall();

    $update->update($data, $routeInfo[2]['id']);
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // Let's check if the csrf token is passed as a query string in the DELETE request
    if (!isset($_GET['csrf_token'])) {
        Output::error('Missing CSRF Token', 401);
        exit();
    }

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        Output::error('Missing user id', 400);
        exit();
    }

    $checks = new Checks($vars, $_GET);

    $checks->checkCSRFDelete($_GET['csrf_token']);

    $id = $routeInfo[2]['id'];

    $delete = new Firewall();

    $delete->delete($id);
}
