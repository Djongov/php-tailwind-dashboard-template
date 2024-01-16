<?php

use Authentication\AzureAD;
use Database\MYSQL;
use Api\Output;
use App\General;
use Authentication\JWT;

// First decide where the auth request is coming from, Azure, local login

// If the request is coming from Azure, we should have a $_POST['id_token'] and a $_POST['state'] variable
if (isset($_POST['id_token'], $_POST['state']) || isset($_POST['error'], $_POST['error_description'])) {
    // if error - throw it as an exception
    if (isset($_POST['error'], $_POST['error_description'])) {
        Output::error("Azure Error: " . $_POST['error'] . " with Description: " . $_POST['error_description'], 200);
    }

    // If someone comes directly from /login, we need to set the state to /
    if ($_POST['state'] === '/login') {
        $_POST['state'] = '/';
    }
    // Let's decide whether the connection is over HTTP or HTTPS (later for setting up the cookie)
    $secure = (str_contains($_SERVER['HTTP_HOST'], 'localhost') || str_contains($_SERVER['HTTP_HOST'], '[::1]')) ? false : true;
    // Let's call the function to check the JWT token which is returned. We are checking stuff like expiration, issuer, app id. We are not validating the signature as per MS article - https://docs.microsoft.com/en-us/azure/active-directory/develop/id-tokens#validating-an-id-token and https://docs.microsoft.com/en-us/azure/active-directory/develop/access-tokens#validating-tokens
    if (AzureAD::checkJWTToken($_POST['id_token'])) {
        // Let's set the "auth_cookie" and put the id token as it's value, set the expiration date to when the token should expire and the rest of the cookie settings
        setcookie(AUTH_COOKIE_NAME, $_POST['id_token'], [
            'expires' => AzureAD::parseJWTTokenPayLoad($_POST['id_token'])['exp'] + 86400,
            'path' => '/',
            'domain' => str_replace(strstr($_SERVER['HTTP_HOST'], ':'), '', $_SERVER['HTTP_HOST']), // strip : from HOST in cases where localhost:8080 is used
            'secure' => $secure, // This needs to be true for most scenarios, we leave the option to be false for local environments
            'httponly' =>  true, // Prevent JavaScript from accessing the cookie
            'samesite' => 'Lax' // This needs to be None otherwise, the trip to ms login endpoint and back will not hold the cookie
        ]);
        // Check if the user is in the DB, if not, create it
        $userCheck = MYSQL::queryPrepared('SELECT * FROM `users` WHERE `username`=?', [AzureAD::parseJWTTokenPayLoad($_POST['id_token'])['preferred_username']]);
        if ($userCheck->num_rows === 0) {
            // Pick the country from the browser language
            $country = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            // Pick the theme from the config
            $theme = COLOR_SCHEME;
            // Create the user in the DB
            $createUser = MYSQL::queryPrepared('INSERT INTO `users`(`username`, `password`, `email`, `name`, `last_ips`, `origin_country`, `role`, `last_login`, `theme`, `provider`, `enabled`) VALUES (?,NULL,?,?,?,?,?,NOW(),?,"azure",1)',
            [
                AzureAD::parseJWTTokenPayLoad($_POST['id_token'])['preferred_username'], // username
                AzureAD::parseJWTTokenPayLoad($_POST['id_token'])['preferred_username'], // email
                AzureAD::parseJWTTokenPayLoad($_POST['id_token'])['name'], // name
                General::currentIP(), // last_ips
                $country, // origin_country
                AzureAD::parseJWTTokenPayLoad($_POST['id_token'])['roles'][0], // role
                $theme // theme
            ]);
            if ($createUser->affected_rows === 1) {
                // Record last login
                MYSQL::recordLastLogin(AzureAD::parseJWTTokenPayLoad($_POST['id_token'])['preferred_username']);
            } else {
                Output::error('User creation failed', 400);
            }
        }
        $destinationUrl = $_POST['state'];
        $destinationUrlScheme = parse_url($destinationUrl)['scheme'] ?? null;

        if ($destinationUrlScheme === 'http://' || $destinationUrlScheme === 'https://'
        ) {
            Output::error('Invalid state', 400);
        }
        header("Location: " . $_POST['state']);
    } else {
        Output::error('Invalid token', 400);
    }
}

// If the request is coming from local login, we should have a $_POST['username'] and a $_POST['password'] variable
if (isset($_POST['username'], $_POST['password'], $_POST['csrf_token'])) {
    // Let's pull the user from the DB and do checks
    $user = MYSQL::queryPrepared('SELECT * FROM `users` WHERE `username`=?', [$_POST['username']]);
    
    if ($user->num_rows === 0) {
        Output::error('Invalid username or password', 400);
    }

    $user = $user->fetch_assoc();

    if ($user['enabled'] === '0') {
        Output::error('User is disabled', 400);
    }

    if (!password_verify($_POST['password'], $user['password'])) {
        Output::error('Invalid username or password', 400);
    }

    // By now we assume the user is valid, so let's generate a JWT token
    
    $idToken = JWT::generateToken([
        'iss' => $_SERVER['HTTP_HOST'],
        'username' => $user['username'],
        'name' => $user['name'],
        'role' => $user['role'],
        'last_ip' => General::currentIP()
    ]);

    // Let's set the auth cookie
    setcookie(AUTH_COOKIE_NAME, $idToken, [
        'expires' => time() + 86400,
        'path' => '/',
        'domain' => str_replace(strstr($_SERVER['HTTP_HOST'], ':'), '', $_SERVER['HTTP_HOST']), // strip : from HOST in cases where localhost:8080 is used
        'secure' => true, // This needs to be true for most scenarios, we leave the option to be false for local environments
        'httponly' =>  true, // Prevent JavaScript from accessing the cookie
        'samesite' => 'Lax' // This needs to be None otherwise, the trip to ms login endpoint and back will not hold the cookie
    ]);
    // Record last login
    MYSQL::recordLastLogin($user['username']);
    // Redirect to the destination
    $destinationUrl = $_POST['state'] ?? '/';
    header("Location: " . $destinationUrl);
    exit();
}


