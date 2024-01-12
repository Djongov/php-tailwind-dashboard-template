<?php
use Authentication\JWT;
// First let's decide the provider of the JWT token
if (JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME])['iss'] === $_SERVER['HTTP_HOST']) {
    $provider = 'local';
} else {
    $provider = 'azure';
}
// If we end up here, then we are relying on our own app registration and the auth JWT token is in the "auth_cookie" so if we want to really log out, we need to clear the cookie.
if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    unset($_COOKIE[AUTH_COOKIE_NAME]);
    setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
}
// Redirect to root page
if ($provider === 'local') {
    header('Location: /');
    exit;
}
if ($provider === 'azure') {
    header('Location: https://login.microsoftonline.com/' . Tenant_ID . '/oauth2/v2.0/logout?post_logout_redirect_uri=https://' . $_SERVER['HTTP_HOST']);
    exit;
}
