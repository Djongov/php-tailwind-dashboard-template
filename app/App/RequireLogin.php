<?php

namespace App;

use Database\DB;
use Authentication\AzureAD;
use Response\DieCode;

class RequireLogin
{
    public static function check()
    {
        $loginExempt = ['/csp-report', '/auth-verify'];

        $loggedIn = false;

        $isAdmin = false;

        $usernameArray = [];

        // If auth cookie exists
        if (isset($_COOKIE['auth_cookie'])) {
            // First check if token is expired and redirect to login URL
            if (!AzureAD::checkJWTTokenExpiry($_COOKIE['auth_cookie'])) {
                header('Location: ' . Login_Button_URL);
            }
            // Check if valid
            if (AzureAD::checkJWTToken($_COOKIE['auth_cookie'])) {
                $loggedIn = true;
            } else {
                // If checks for JWT token fail - unset cookie and redirect to /login
                unset($_COOKIE['auth_cookie']);
                setcookie('auth_cookie', false, -1, '/', $_SERVER["HTTP_HOST"]);
                header('Location: /login');
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
            }
        }

        $idTokenInfoArray = [];

        if ($loggedIn && isset($_COOKIE['auth_cookie'])) {
            // Let's parse the JWT token from the auth cookie and look at the claims
            $authCookieArray = AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie']);
            // We are mapping what the claims are called in the DB (keys) vs in the JWT token (values)
            $expectedClaims = [
                'username' => 'preferred_username',
                'email' => 'email',
                'name' => 'name',
                'last_ip' => 'ipaddr',
                'country' => 'ctry'
            ];
            foreach ($expectedClaims as $dbClaimName => $JWTClaimName) {
                $idTokenInfoArray[$dbClaimName] = $authCookieArray[$JWTClaimName];
            }
            // Now the special ones
            $idTokenInfoArray["token_expiry"] = isset($authCookieArray['exp']) ? date("Y-m-d H:i:s", substr($authCookieArray['exp'], 0, 10)) : null;
            // roles
            $idTokenInfoArray["role"] = (isset($authCookieArray['roles'])) ? $authCookieArray['roles'] : null;
            // Now we search for the administrator role as role is an array of roles
            foreach ($idTokenInfoArray["role"] as $role) {
                if ($role === 'administrator') {
                    $idTokenInfoArray["role"] = 'administrator';
                }
            }
        }
        
        $username = ($loggedIn) ? $idTokenInfoArray["username"] : null;

        // If we are logged in and we have an established username, we need to either fetch user data from the DB or create a new user in the DB
        if ($username !== null) {
            $userResult = DB::query("SELECT * FROM `users` WHERE `username` = '$username'");
            if ($userResult->num_rows > 0) {
                $usernameArray = $userResult->fetch_assoc();
                // Let's check if the last_ip is different from what we have in the DB, and update it
                if ($usernameArray['last_ips'] !== $idTokenInfoArray["last_ip"]) {
                    DB::queryPrepared("UPDATE `users` SET `last_ips`=? WHERE `username`=?",[$idTokenInfoArray["last_ip"], $username]);
                } 
            } else {
                $user_exists_check = DB::queryPrepared("SELECT `username` FROM `users` WHERE `username`=?", $username);
                if ($user_exists_check->num_rows === 0 && $loggedIn) {
                    $caputredEmail = (isset($idTokenInfoArray["email"])) ? $idTokenInfoArray["email"] : null;
                    DB::queryPrepared("INSERT INTO `users`(`username`, `email`, `name`, `last_ips`, `origin_country`, `role`, `last_login`, `theme`, `enabled`) VALUES (?,?,?,?,?,?,NOW(),'amber', '1')", [$idTokenInfoArray["username"], $caputredEmail, $idTokenInfoArray["name"], $idTokenInfoArray["last_ip"], $idTokenInfoArray["country"], $idTokenInfoArray["role"]]);
                    $newUserResult = DB::queryPrepared("SELECT * FROM `users` WHERE `username` = ?", [$idTokenInfoArray["username"]]);
                    $usernameArray = $newUserResult->fetch_assoc();
                    //writeToSystemLog("Created a new user with info - " . implode(" | ", $idTokenInfoArray), "Authentication");
                }
            }
            // Now some admin checks
            // If there is a role = administrator in both token and DB, give admin straight away
            if ($idTokenInfoArray["role"] === 'administrator' && $usernameArray['role'] === 'administrator') {
                $isAdmin = true;
            // If the JWT token has role admin but the DB says NO, it might have been a new assignment of admin role so we need to update DB
            } elseif ($idTokenInfoArray["role"] === 'administrator' && $usernameArray['role'] !== 'administrator') {
                DB::queryPrepared("UPDATE `users` SET `role`='administrator' WHERE `username`=?", [$usernameArray['username']]);
                // Good to alert as well
            // And if DB says admin but the JWT token no longer bears the admin role - remove it
            } elseif ($idTokenInfoArray["role"] !== 'administrator' && $usernameArray['role'] === 'administrator') {
                DB::queryPrepared("UPDATE `users` SET `role`=NULL WHERE `username`=?", [$usernameArray['username']]);
                // Good to alert as well
            }
        }

        // Kill disabled users early
        if (isset($usernameArray["enabled"]) && $usernameArray["enabled"] === 0) {
            DieCode::kill('Your user has been disabled', 401);
        }

        //$theme = (isset($usernameArray["theme"])) ? $usernameArray["theme"] : COLOR_SCHEME;
        //$theme = $usernameArray['theme'] ?? COLOR_SCHEME;

        return [
            'usernameArray' => $usernameArray,
            'loggedIn' => $loggedIn,
            'isAdmin' => $isAdmin,
        ];

    }
}