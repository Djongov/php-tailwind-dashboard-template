<?php
// If we end up here, then we are relying on our own app registration and the auth JWT token is in the "auth_cookie" so if we want to really log out, we need to clear the cookie.
if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    unset($_COOKIE[AUTH_COOKIE_NAME]);
    setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
}
// Redirect to root page if provider is local
if ($usernameArray['provider'] === 'local') {
    header('Location: /');
    exit;
}
// Send to AzureAD logout URL if provider is AzureAD
if ($usernameArray['provider'] === 'azure') {
    header('Location: ' . Logout_Button_URL);
    exit;
}
