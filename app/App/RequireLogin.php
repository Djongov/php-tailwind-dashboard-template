<?php

namespace App;

use Database\MYSQL;
use Authentication\AzureAD;
use Authentication\JWT;
use Api\Output;

class RequireLogin
{
    public static function check()
    {
        $loginExempt = ['/', '/docs', '/docs/example', '/csp-report', '/register', '/auth-verify', '/api/user'];

        $loggedIn = false;

        $isAdmin = false;

        $usernameArray = [];

        $username = null;

        $provider = '';

        // If auth cookie exists
        if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
            // First parse the JWT token
            $tokenPayload = JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME]);

            // If the issuer is $_SERVER['HTTP_HOST'], we are dealing with a local login
            if ($tokenPayload['iss'] === $_SERVER['HTTP_HOST']) {
                // Check if valid
                if (JWT::checkToken($_COOKIE[AUTH_COOKIE_NAME])) {
                    $provider = 'local';
                    $loggedIn = true;
                } else {
                    // If checks for JWT token fail - unset cookie and redirect to /login
                    JWT::handleValidationFailure();
                    header('Location: /login?destination=' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            }

            // Now check if the issuer is the AzureAD endpoint
            if (str_starts_with($tokenPayload['iss'], 'https://login.microsoftonline.com/')) {
                // Check if valid
                if (AzureAD::check($_COOKIE[AUTH_COOKIE_NAME])) {
                    $provider = 'azure';
                    $loggedIn = true;
                } else {
                    // If checks for JWT token fail - unset cookie and redirect to /login
                    JWT::handleValidationFailure();
                    header('Location: /login?destination=' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            }
        } else {
            // Redirect to /login but preserve the destination if auth_cookie is missing
            /*
                Do not redirect to /login if uri is in the list or exempt urls or
                if you expect query strings: add this to the below if
                && !str_contains($_SERVER['REQUEST_URI'], 'view-report')
            */
            if (!in_array($_SERVER['REQUEST_URI'], $loginExempt) && !str_contains($_SERVER['REQUEST_URI'], '/login')) {
                header('Location: /login?destination=' . $_SERVER['REQUEST_URI']);
                exit();
            }
        }

        $idTokenInfoArray = [];

        if ($loggedIn && isset($_COOKIE[AUTH_COOKIE_NAME]) && $provider === 'azure') {
            // Let's parse the JWT token from the auth cookie and look at the claims
            $authCookieArray = $tokenPayload;
            // We are mapping what the claims are called in the DB (keys) vs in the JWT token (values)
            $expectedClaims = [
                'username' => 'preferred_username',
                'email' => 'email',
                'name' => 'name',
                'last_ip' => 'ipaddr',
                'country' => 'ctry'
            ];
            foreach ($expectedClaims as $dbClaimName => $JWTClaimName) {
                $idTokenInfoArray[$dbClaimName] = isset($authCookieArray[$JWTClaimName]) ? $authCookieArray[$JWTClaimName] : null;
            }
            // Now the special ones
            $idTokenInfoArray["token_expiry"] = isset($authCookieArray['exp']) ? date("Y-m-d H:i:s", substr($authCookieArray['exp'], 0, 10)) : null;
            // roles
            $idTokenInfoArray["role"] = (isset($authCookieArray['roles'])) ? $authCookieArray['roles'] : null;
            // Now we search for the administrator role as role is an array of roles
            if (isset($idTokenInfoArray["role"])) {
                foreach ($idTokenInfoArray["role"] as $role) {
                    if ($role === 'administrator') {
                        $idTokenInfoArray["role"] = 'administrator';
                    }
                }
            }

            $username = ($loggedIn) ? $idTokenInfoArray["username"] : null;
        }

        if ($provider === 'local') {
            $username = $tokenPayload['username'];
            $usernameArray = $tokenPayload;
            $idTokenInfoArray = $tokenPayload;
        }
        

        // If we are logged in and we have an established username, we need to either fetch user data from the DB or create a new user in the DB
        if ($username !== null) {
            $userResult = MYSQL::query("SELECT * FROM `users` WHERE `username` = '$username'");
            if ($userResult->num_rows > 0) {
                $usernameArray = $userResult->fetch_assoc();
                // Let's check if the last_ip is different from what we have in the DB, and update it
                if ($usernameArray['last_ips'] !== $idTokenInfoArray["last_ip"]) {
                    MYSQL::queryPrepared("UPDATE `users` SET `last_ips`=? WHERE `username`=?",[$idTokenInfoArray["last_ip"], $username]);
                }
            } else {
                header('Location: /logout');
                exit();
            }
            // Now some admin checks
            // If there is a role = administrator in both token and DB, give admin straight away
            if ($idTokenInfoArray["role"] === 'administrator' && $usernameArray['role'] === 'administrator') {
                $isAdmin = true;
            // If the JWT token has role admin but the DB says NO, it might have been a new assignment of admin role so we need to update DB
            } elseif ($idTokenInfoArray["role"] === 'administrator' && $usernameArray['role'] !== 'administrator') {
                MYSQL::queryPrepared("UPDATE `users` SET `role`='administrator' WHERE `username`=?", [$usernameArray['username']]);
                // Good to alert as well
            
            }
            // And if DB says admin but the JWT token no longer bears the admin role - remove it
            // elseif ($idTokenInfoArray["role"] !== 'administrator' && $usernameArray['role'] === 'administrator') {
            //     MYSQL::queryPrepared("UPDATE `users` SET `role`=NULL WHERE `username`=?", [$usernameArray['username']]);
            //     // Good to alert as well
            // }
        }

        // Kill disabled users early
        if (isset($usernameArray["enabled"]) && $usernameArray["enabled"] === 0) {
            Output::error('Your user has been disabled', 401);
        }

        //$theme = (isset($usernameArray["theme"])) ? $usernameArray["theme"] : COLOR_SCHEME;
        //$theme = $usernameArray['theme'] ?? COLOR_SCHEME;

        // If this gets executed on /login, we need to keep logged in users away from the login page
        if (str_contains($_SERVER['REQUEST_URI'], 'login') && $loggedIn) {
            if (isset($_GET['destination'])) {
                header('Location: ' . $_GET['destination']);
                exit();
            } else {
                header('Location: /');
                exit();
            }
        }

        return [
            'usernameArray' => $usernameArray,
            'loggedIn' => $loggedIn,
            'isAdmin' => $isAdmin,
        ];

    }
}
