<?php

use Api\Output;
use Api\Checks;
use Api\User;
use Database\MYSQL;
use App\General;

// This is the API endpoint controller for the user actions

// POST /api/user/create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $checks = new Checks($vars, $_POST);
    $checks->apiChecksNoUser();

    // Create the user
    $user = new User();
    
    $data = $_POST;

    unset($data['csrf_token']);

    $data['last_ips'] = General::currentIP();

    $data['origin_country'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

    $data['role'] = 'user';

    $data['theme'] = COLOR_SCHEME;

    $data['provider'] = 'local';

    $data['enabled'] = 1;

    echo $user->createLocalUser($data);
}


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Let's catch php input stream
    $data = Checks::jsonBody();

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        echo Output::error('Missing user id');
        exit();
    }

    $userId = $routeInfo[2]['id'];

    $checks = new Checks($vars, $data);
    $checks->apiChecks();

    if (isset($data['passwword'], $data['confirm_password'])) {
        if ($data['password'] !== $data['confirm_password']) {
            echo Output::error('Passwords do not match');
            exit();
        }
    }

    if (isset($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    unset($data['confirm_password']);
    unset($data['csrf_token']);

    $user = new User();
    $user->update($data, $userId);

}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    // Let's check if the csrf token is passed as a query string in the DELETE request
    if (!isset($_GET['csrf_token'])) {
        echo Output::error('Missing CSRF Token');
        exit();
    }

    // Let's catch php input stream
    $data = Checks::jsonBody();

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        echo Output::error('Missing user id');
        exit();
    }

    $userId = $routeInfo[2]['id'];

    $checks = new Checks($vars, []);
    $checks->apiChecksDelete($_GET['csrf_token']);

    $deleteUser = MYSQL::queryPrepared('DELETE FROM `users` WHERE `id`=?', [$userId]);
    if ($deleteUser->affected_rows === 0) {
        echo Output::error('User not found');
        exit();
    } else {
        echo Output::success('User deleted');
        exit();
    }
}
