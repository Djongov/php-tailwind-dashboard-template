<?php

use Authentication\AzureAD;
use Database\MYSQL;
use Response\DieCode;

// if error - throw it as an exception
if (isset($_POST['error'], $_POST['error_description'])) {
    DieCode::kill("Azure Error: " . $_POST['error'] . " with Description: " . $_POST['error_description'], 200);
}
// state is required
if (!isset($_POST['state'])) {
    DieCode::kill('Missing state', 400);
}
// However, if all good, we should be returning with an argument called id_token
if (isset($_POST['id_token'])) {
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
        // Update the last login time in the DB
        MYSQL::recordLastLogin(AzureAD::parseJWTTokenPayLoad($_POST['id_token'])['preferred_username']);
        $destinationUrl = $_POST['state'];
        $destinationUrlScheme = parse_url($destinationUrl)['scheme'] ?? null;

        if ($destinationUrlScheme === 'http://' || $destinationUrlScheme === 'https://') {
            DieCode::kill('Invalid state', 400);
        }
        header("Location: " . $_POST['state']);
    } else {
        DieCode::kill('Invalid token', 400);
    }
} else {
    DieCode::kill('Missing token', 400);
}
