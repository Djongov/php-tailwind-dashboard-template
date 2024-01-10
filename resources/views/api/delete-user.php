<?php

use Api\Output;
use Authentication\AzureAD;
use Database\MYSQL;

//return var_dump($loginInfoArray);

$isInvalidCSRF = (!isset($_SESSION['csrf_token']) ||
    !isset($_POST['csrf_token']) ||
    $_SESSION['csrf_token'] !== $_POST['csrf_token']
);

if ($isInvalidCSRF) {
    Output::error('Incorrect CSRF token', 401);
}

if ($loginInfoArray['usernameArray']['username'] !== $_POST['username']) {
    Output::error('Username missmatch', 401);
}

if (!isset($_COOKIE[AUTH_COOKIE_NAME])) {
    Output::error('No Authentication present', 401);
}

if (isset($_COOKIE[AUTH_COOKIE_NAME]) && !AzureAD::checkJWTTokenExpiry($_COOKIE[AUTH_COOKIE_NAME])) {
    Output::error('Authentication token expired', 401);
}

$userCheck = MYSQL::queryPrepared("SELECT * FROM `users` WHERE `username`=?", $_POST['username']);

if ($userCheck->num_rows === 0) {
    Output::error('User not found', 404);
}

MYSQL::queryPrepared("DELETE FROM `users` WHERE `username`=?", $_POST['username']);

if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    unset($_COOKIE[AUTH_COOKIE_NAME]);
    setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
}

header("Refresh:0");
exit;
