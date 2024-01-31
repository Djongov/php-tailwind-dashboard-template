<?php
// If we end up here, then we are relying on our own app registration and the auth JWT token is in the "auth_cookie" so if we want to really log out, we need to clear the cookie.
use Authentication\JWT;

JWT::handleValidationFailure();

if (!isset($usernameArray['provider']) || empty($usernameArray['provider'])) {
    header('Location: /');
    exit;
}
// Redirect to root page if provider is local
if ($usernameArray['provider'] === 'local') {
    header('Location: /');
    exit;
}
// Send to AzureAD logout URL if provider is AzureAD
if ($usernameArray['provider'] === 'azure') {
    header('Location: ' . LOGOUT_BUTTON_URL);
    exit;
}
