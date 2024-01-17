<?php

use Api\Output;
use Database\MYSQL;
use Authentication\Checks;

$checks = new Checks();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Secret header checks
    $checks->checkSecretHeader();
    // Let's catch php input stream
    $putData = file_get_contents('php://input');
    // Now we need to get the put data and make into array
    $putData = json_decode($putData, true);
    // Check if the put data is an array
    if ($putData === null) {
        Output::error('array cannot be null.', 400);
    }
    // Here we are expecting the following keys in the array
    $expectedKeys = ['password', 'confirm_password', 'csrf_token'];
    // Check if the put data has the expected keys
    foreach ($expectedKeys as $key) {
        if (!array_key_exists($key, $putData)) {
            Output::error('Missing key: ' . $key, 400);
        }
    }
    $checks->checkCSRF($putData['csrf_token']);
    // Remove from putData array csrf_token
    unset($putData['csrf_token']);
    
    $checks->usernameIntegrationCheck($vars);

    // make sure the password and confirm_password are the same
    if ($putData['password'] !== $putData['confirm_password']) {
        echo Output::error('Passwords do not match');
        exit;
    }

    // Some username checks

    $userId = $routeInfo[2]['id'];

    // Check if the user exists
    $userExists = MYSQL::queryPrepared('SELECT * FROM `users` WHERE `id`=? AND `username`=?', [$userId, $vars['usernameArray']['username']]);

    if ($userExists->num_rows === 0) {
        echo Output::error('User does not exist');
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($putData['password'], PASSWORD_DEFAULT);

    // Update the password
    $updatePassword = MYSQL::queryPrepared('UPDATE `users` SET `password`=? WHERE `id`=? AND `username`=?', [$hashedPassword, $vars['usernameArray']['id'], $vars['usernameArray']['username']]);

    if ($updatePassword->affected_rows === 0) {
        echo Output::error('Nothing updated');
    } else {
        echo Output::success('Password updated');
    }
}
