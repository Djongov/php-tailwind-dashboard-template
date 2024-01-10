<?php

use Api\Output;
use Database\MYSQL;
use Authentication\AzureAD;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Pick up the X-CSRF-TOKEN header
    $headers = getallheaders();
    //return var_dump($headers);
    if (!isset($headers['X-Csrf-Token'])) {
        return Output::error('Missing CSRF Token');
    }
    $csrfToken = $headers['X-Csrf-Token'];
    if (!isset($headers['Secretheader'])) {
        return Output::error('Missing Secret Header');
    }
    $secretHeader = $headers['Secretheader'];
    // Compare the csrfToken to the $_SESSION['csrf_token']
    if ($csrfToken !== $_SESSION['csrf_token']) {
        return Output::error('Invalid CSRF Token');
    }
    // Now check if secret header is correct
    if ($secretHeader !== 'badass') {
        return Output::error('Invalid Secret Header');
    }
    // Now check the if the user is logged in
    if (!$vars['loggedIn']) {
        return Output::error('You are not logged in');
    }
    // Now check if the user submitting this is the same as the user being deleted
    if ($vars['usernameArray']['id'] !== intval($routeInfo[2]['id'])) {
        return Output::error('You are not allowed to delete this user');
    }
    // Let's pick up the username from the user id from the DB
    $userInfo = MYSQL::queryPrepared('SELECT `username` FROM `users` WHERE `id`=?', [$routeInfo[2]['id']]);
    if ($userInfo->num_rows === 0) {
        return Output::error('User not found');
    }
    $username = $userInfo->fetch_assoc()['username'];
    // Now compare the username to the username in the vars['usernameArray']
    if ($username !== $vars['usernameArray']['username']) {
        return Output::error('You are not allowed to delete this user');
    }
    // Now compare the username to the cookie username
    $cookieUsername = AzureAD::extractUserName($_COOKIE[AUTH_COOKIE_NAME]);
    if ($username !== $cookieUsername) {
        return Output::error('You are not allowed to delete this user');
    }
    // Now delete the user
    $deleteUser = MYSQL::queryPrepared('DELETE FROM `users` WHERE `id`=?', [$routeInfo[2]['id']]);
    if ($deleteUser->affected_rows === 0) {
        return Output::error('User not found');
    }
}
