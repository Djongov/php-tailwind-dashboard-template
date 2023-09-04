<?php

use Response\DieCode;
use Authentication\AzureAD;
use Database\DB;

//return var_dump($loginInfoArray);

$isInvalidCSRF = (!isset($_SESSION['csrf_token']) ||
    !isset($_POST['csrf_token']) ||
    $_SESSION['csrf_token'] !== $_POST['csrf_token']
);

if ($isInvalidCSRF) {
    DieCode::kill('Incorrect CSRF token', 401);
}

if ($loginInfoArray['usernameArray']['username'] !== $_POST['username']) {
    DieCode::kill('Username missmatch', 401);
}

if (!isset($_COOKIE[AUTH_COOKIE_NAME])) {
    DieCode::kill('No Authentication present', 401);
}

if (isset($_COOKIE[AUTH_COOKIE_NAME]) && !AzureAD::checkJWTTokenExpiry($_COOKIE[AUTH_COOKIE_NAME])) {
    DieCode::kill('Authentication token expired', 401);
}

$userCheck = DB::queryPrepared("SELECT * FROM `users` WHERE `username`=?", $_POST['username']);

if ($userCheck->num_rows === 0) {
    DieCode::kill('User not found', 404);
}

DB::queryPrepared("DELETE FROM `users` WHERE `username`=?", $_POST['username']);

if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    unset($_COOKIE[AUTH_COOKIE_NAME]);
    setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
}

header("Refresh:0");
exit;
