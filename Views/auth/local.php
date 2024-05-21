<?php

declare(strict_types=1);


use Controllers\Api\User;
use Controllers\Api\Output;
use App\Exceptions\UserExceptions;
use Controllers\Api\Checks;
use App\Logs\SystemLog;
use App\Authentication\JWT;
use App\Core\Cookies;
use App\Utilities\IP;

// If the request is coming from local login, we should have a $_POST['username'] and a $_POST['password'] parameter
if (isset($_POST['username'], $_POST['password'], $_POST['csrf_token'])) {
    // First check the CSRF token
    $checks = new Checks($vars, $_POST);

    $checks->checkCSRF($_POST['csrf_token']);

    // You can implement a sleep here, to slow down the response to and therefore slow down potential spam on the login form
    sleep(0);

    $user = new User();

    try {
        $userArray = $user->get($_POST['username']);
    } catch (UserExceptions $e) {
        Output::error($e->getMessage());
    } catch (\Exception $e) {
        SystemLog::write('Generic error when trying to get local user ' . $_POST['username'] . ' with error: ' . $e->getMessage(), 'User API');
        Output::error('error', 400);
    }
    
    if (empty($userArray)) {
        Output::error('Invalid username or password', 404); // Do not say if the user exists or not to reduce the risk of enumeration attacks
    }

    if ($userArray['enabled'] === '0') {
        Output::error('User is disabled', 401);
    }

    if (!password_verify($_POST['password'], $userArray['password'])) {
        Output::error('Invalid username or password', 404);
    }

    // By now we assume the user is valid, so let's generate a JWT token
    
    $idToken = JWT::generateToken([
        'iss' => $_SERVER['HTTP_HOST'],
        'username' => $userArray['username'],
        'name' => $userArray['name'],
        'roles' => [
            $userArray['role'],
        ],
        'last_ip' => IP::currentIP()
    ]);

    $expiry_addition = ($_POST['remember'] === "1") ? 86400 * 24 * 12 : 86400;
    
    Cookies::setAuthCookie($idToken, $expiry_addition);
    // Record last login
    $user->updateLastLogin($userArray['username']);

    $destinationUrl = $_POST['state'] ?? null;
    if ($destinationUrl !== null && (substr($destinationUrl, 0, 1) === '/')) {
        // Invalid destination or state, set a default state
        $destinationUrl = '/';
    }
    // Valid destination, proceed with your script
    header("Location: " . filter_var($destinationUrl, FILTER_SANITIZE_URL));
    exit();
}
