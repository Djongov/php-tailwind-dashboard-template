<?php
// If we end up here, then we are relying on our own app registration and the auth JWT token is in the "auth_cookie" so if we want to really log out, we need to clear the cookie.
if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    unset($_COOKIE[AUTH_COOKIE_NAME]);
    setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
}
// Redirect to root page
header("location: https://login.microsoftonline.com/" . Tenant_ID . "/oauth2/v2.0/logout?post_logout_redirect_uri=https://" . $_SERVER['HTTP_HOST']);
exit;
