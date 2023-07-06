<?php

namespace App;

use Database\DB;
use Authentication\AzureAD;
use Response\DieCode;

class RequireLogin
{
    public static function check()
    {
        $loginExempt = [];

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
            // Let's save the username and try to display it later when logged in
            $idTokenInfoArray["username"] = AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['preferred_username'];
            $idTokenInfoArray["email"] = AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['email'];
            $idTokenInfoArray["name"] = AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['name'];
            $idTokenInfoArray["last_ip"] = (isset(AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['ipaddr'])) ? AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['ipaddr'] : null;
            $idTokenInfoArray["country"] = (isset(AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['ctry'])) ? AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['ctry'] : null;
            $idTokenInfoArray["token_expiry"] = date("Y-m-d H:i:s", substr(AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['exp'], 0, 10));
            $idTokenInfoArray["role"] = (isset(AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['roles'])) ? AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['roles'][0] : null;
        }

        $username = ($loggedIn) ? $idTokenInfoArray["username"] : null;

        // If we are logged in and we have an established username, we need to either fetch user data from the DB or create a new user in the DB
        if ($username !== null) {
            $userResult = DB::query("SELECT * FROM `users` WHERE `username` = '$username'");
            if ($userResult->num_rows > 0) {
                $usernameArray = $userResult->fetch_assoc();
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
            // In order to get admin status, you need to have administrator role in the JWT token as well as in the DB
            if ($idTokenInfoArray["role"] === 'administrator' && $usernameArray['role'] === 'administrator') {
                $isAdmin = true;
                // If the JWT token is still administrator but DB says not administrator, isAdmin remains false but we need to be informed. Also log it in the system log
                // It might be the case that a user has already been a member of the portal and has been given admin later (from the enterprise application). In that case, it's still better to alert us and we make the user manually an admin in the DB
            } elseif ($idTokenInfoArray["role"] === 'administrator' && $usernameArray['role'] !== 'administrator') {
                //writeToSystemLog($usernameArray['username'] . ' has just been denied admin rights because the JWT token has admin role but the user in the DB doesn\'t', 'Access');
                //include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/functions/sendMail/sendMail.php';
                //sendMail(ADMINISTRATOR, 'Administrator missmatch at ' . SITE_TITLE, $usernameArray['username'] . ' has just been denied admin rights because the JWT token has admin role but the user in the DB doesn\'t');
                // And the opposite. However the opposite means that the user has been removed from the admin at the app registration level, which is the initial source of admin rights by design. Therefore, we also immediately take action and remove the administrator role from the DB too, not because of security, we still don't provide admin access but the we are sending alerts and emails to admins and the spam will be huge
            } elseif ($idTokenInfoArray["role"] !== 'administrator' && $usernameArray['role'] === 'administrator') {
                //writeToSystemLog($usernameArray['username'] . ' has just been denied admin rights because the JWT token does not have admin role but the user in still an admin in the DB', 'Access');
                //include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/functions/sendMail/sendMail.php';
                //sendMail(ADMINISTRATOR, 'Administrator missmatch at ' . SITE_TITLE, $usernameArray['username'] . ' has just been denied admin rights because the JWT token does not have admin role but the user in still an admin in the DB');
                // Now the action to remove the admin from the DB too. It's good to have it because with the above checks and alerts, the spam will be huge
                DB::queryPrepared("UPDATE `users` SET `role`=NULL WHERE `username`=?", [$usernameArray['username']]);
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