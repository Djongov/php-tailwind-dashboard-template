<?php
use Api\Output;
use App\General;
use Database\MYSQL;
use Authentication\Checks;

// Basic check if username, password, confirm_password are set
if (!isset($_POST['username'], $_POST['password'], $_POST['confirm_password'], $_POST['name'], $_POST['csrf_token'])) {
    Output::error('Missing required fields', 400);
}
// Now if they are empty
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
    Output::error('Empty required fields', 400);
}
// First basic check if passwords match
if ($_POST['password'] !== $_POST['confirm_password']) {
    Output::error('Passwords do not match', 400);
}


$checks = new Checks();
$checks->genericChecks($vars);
$checks->checkSecretHeader();
$checks->checkCSRF($_POST['csrf_token']);
// Now check if the username is a valid email
// if (!filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)) {
//     Output::error('Invalid username, must be an email', 400);
// }

// if email is passed, check if it's valid
if (isset($_POST['email'])) {
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        Output::error('Invalid email', 400);
    }
}

$username = $_POST['username'];
$password = $_POST['password'];
$name = $_POST['name'];
$lastIp = General::currentIP();
// Pick the country from the browser language
$country = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$theme = COLOR_SCHEME;

// Now let's check if the username is already taken
$user = MYSQL::queryPrepared('SELECT * FROM `users` WHERE `username`=?', [$username]);

if ($user->num_rows > 0) {
    Output::error('Username already taken', 409);
}

// Now let's hash the password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Now let's insert the user into the DB
$createUser = MYSQL::queryPrepared('INSERT INTO `users`(`username`, `password`, `email`, `name`, `last_ips`, `origin_country`, `role`, `last_login`, `theme`, `enabled`) VALUES (?,?,?,?,?,?,?,NOW(),?,?)', [$username, $passwordHash, $username, $name, $lastIp, $country, 'user', $theme, '1']);

if ($createUser) {
    echo Output::success('User created');
} else {
    echo Output::error('User creation failed', 400);
}
