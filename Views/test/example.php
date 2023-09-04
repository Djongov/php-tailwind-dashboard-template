<?php
use Response\DieCode;
use Authentication\AzureAD;

if (!isset($_COOKIE['auth_cookie'])) {
    DieCode::kill('No Authentication present', 401);
}

if (isset($_COOKIE['auth_cookie']) && !AzureAD::checkJWTTokenExpiry($_COOKIE['auth_cookie'])) {
    DieCode::kill('Authentication token expired', 401);
}

$chance = rand(0, 1);

echo '<pre>' . json_encode($_POST, JSON_PRETTY_PRINT) . '</pre>';
