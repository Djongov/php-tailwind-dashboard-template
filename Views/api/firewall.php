<?php

use Controllers\Api\Output;
use Controllers\Api\Checks;
use Controllers\Api\Firewall;

// This is the API view for the firewall. It allows to add, update, delete and get IPs from the firewall

// api/firewall GET, accepts a "cidr" parameter in the query string. If no query string provided, returns all IPs in the firewall table.
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // This endpoint is for creating a new local user. Cloud users are create in /auth-verify

    // We only allow either an empty GET or a GET with a "cidr" parameter
    if (!empty($_GET) && !isset($_GET['cidr'])) {
        Output::error('parameters accepted are "cidr" or empty GET', 400);
    }

    $checks = new Checks($vars, $_GET);
    $checks->apiChecksNoCSRF();

    // check if cidr has been passed
    if (!isset($_GET['cidr'])) {
        $ip = '';
    } else {
        $ip = $_GET['cidr'];
    }

    $firewall = new Firewall();

    echo $firewall->get($ip);
}

// api/firewall POST, accepts form data with the "cidr" parameter and optional "comment". The user making the request is taken from the router data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checks = new Checks($vars, $_POST);
    $checks->apiChecks();

    $checks->checkParams(['cidr'], $_POST);

    $comment = $_POST['comment'] ?? '';

    $createdBy = $vars['usernameArray']['username'];

    $ip = $_POST['cidr'];

    $save = new Firewall();

    echo $save->add($ip, $createdBy, $comment);
}

// api/firewall/{id} PUT, accepts json body wit the data to update, id in the path. The user making the request is taken from the router data
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Check if content type is json
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        Output::error('content type must be application/json', 400);
        exit();
    }
    // Let's catch php input stream
    $data = Checks::jsonBody();

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        Output::error('missing id paramter', 400);
        exit();
    }

    $update = new Firewall();

    $updatedBy = $vars['usernameArray']['username'];

    echo $update->update($data, $routeInfo[2]['id'], $updatedBy);
}

// api/firewall/{id}?csrf_token={} DELETE, empty body, param in path, csrf token in query string. The user making the request is taken from the router data
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check if body is empty
    if ($_SERVER['CONTENT_LENGTH'] > 0) {
        Output::error('body must be empty in DELETE requests', 400);
        exit();
    }
    // Let's check if the csrf token is passed as a query string in the DELETE request
    if (!isset($_GET['csrf_token'])) {
        Output::error('missing csrf token', 401);
        exit();
    }

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        Output::error('missing id parameter', 400);
        exit();
    }

    $checks = new Checks($vars, $_GET);

    $checks->checkCSRFDelete($_GET['csrf_token']);

    $id = $routeInfo[2]['id'];

    $delete = new Firewall();

    $deletedBy = $vars['usernameArray']['username'];

    echo $delete->delete($id, $deletedBy);
}
