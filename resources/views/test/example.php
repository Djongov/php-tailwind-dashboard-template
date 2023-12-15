<?php
use Response\DieCode;
use Authentication\AzureAD;

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

echo '<pre>' . json_encode($_POST, JSON_PRETTY_PRINT) . '</pre>';
