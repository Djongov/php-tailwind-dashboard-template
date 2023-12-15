<?php

use Database\MYSQL;
use Response\DieCode;

$isInvalidCSRF = (!isset($_SESSION['csrf_token']) ||
    !isset($_POST['csrf_token']) ||
    $_SESSION['csrf_token'] !== $_POST['csrf_token']
);

if ($isInvalidCSRF) {
    DieCode::kill('Incorrect CSRF token', 401);
}

$expectedParams = ['username', 'password', 'csrf_token'];

foreach($expectedParams as $param) {
    if (empty($param)) {
        DieCode::kill('empty' . $param, 404);
    }
}

$usernameCheck = MYSQL::queryPrepared("SELECT * FROM `local_users` WHERE `username`=?", $_POST['username']);

if ($usernameCheck->num_rows === 0) {
    DieCode::kill('No such username', 404);
}

$usernameArray = $usernameCheck->fetch_assoc();

//return var_dump($usernameArray);

$hashedPassword = $usernameArray['password'];

$passwordCheck = password_verify($_POST['password'], $hashedPassword);

if ($passwordCheck) {
    header('Location:' . $_POST['destination']);
} else {
    DieCode::kill('Incorrect password', 404);
}
