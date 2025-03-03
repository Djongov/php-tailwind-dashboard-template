<?php declare(strict_types=1);

use App\Api\Response;
use App\Api\Checks;
use Controllers\Api\User;
use App\Authentication\JWT;
use App\Authentication\AuthToken;
use App\Logs\SystemLog;

// GET /api/user
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $checks = new Checks($vars, []);
    $checks->apiChecksNoCSRF();

    $user = new User();

    if (!$routeInfo[2]) {
        $allUsers = $user->get(null);
        if ($allUsers) {
            Response::output($allUsers, 200);
        } else {
            Response::output('No users found', 404);
        }
        return;
    }

    // This endpoint is for fetching a user's data
    if (!isset($routeInfo[2]['id'])) {
        Response::output('Missing user id', 400);
        exit();
    }

    // If the user is integer, then we will assume it's an id, otherwise we'll assume it's a username
    $userId = $routeInfo[2]['id'];
    if (!is_numeric($userId)) {
        $userId = (string) $userId;
    } else {
        $userId = (int) $userId;
    }

    $userInfoArray = $user->get($userId);

    if ($userInfoArray) {
        Response::output($userInfoArray, 200);
    } else {
        Response::output('User not found', 404);
    }
}

// POST /api/user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!MANUAL_REGISTRATION) {
        Response::output('Manual registration is disabled', 400);
        exit();
    }
    // This endpoint is for creating a new local user.
    $checks = new Checks($vars, $_POST);
    $checks->apiChecksNoUser();

    // Create the user
    $user = new User();

    $data = $_POST;

    unset($data['csrf_token']);

    $requiredFields = ['username', 'password', 'confirm_password', 'email'];

    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            Response::output('Missing ' . $field, 400);
            exit();
        }
        if (empty($data[$field])) {
            Response::output('Empty ' . $field, 400);
            exit();
        }
    }

    $data['last_ips'] = currentIP();

    $data['origin_country'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : 'EN';

    $data['role'] = 'user';

    $data['theme'] = COLOR_SCHEME;

    $data['provider'] = 'local';

    $data['enabled'] = 1;

    // Decide whether you want to sleep here or not, to slow down the brute force attacks
    // sleep(2);

    echo $user->create($data, 'local');
}


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Let's catch php input stream
    $data = Checks::jsonBody();

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        Response::output('Missing user id', 400);
        exit();
    }

    $userId = (int) $routeInfo[2]['id'];

    $checks = new Checks($vars, $data);
    $checks->apiChecks();

    // Get the user data based on the ID
    $user = new User();

    $dbUserData = $user->get($userId);

    if (!$dbUserData) {
        Response::output('User not found', 404);
        exit();
    }
    
    // Some checks
    if ($dbUserData['id'] !== $userId) {
        Response::output('You cannot edit another user\'s data', 401);
        exit();
    }

    if (isset($data['username']) && JWT::extractUserName(AuthToken::get()) !== $data['username']) {
        Response::output('Username token missmatch', 409);
        exit();
    }

    if (isset($data['passwword'], $data['confirm_password'])) {
        if ($data['password'] !== $data['confirm_password']) {
            Response::output('Passwords do not match', 400);
            exit();
        }
    }

    if (isset($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    unset($data['confirm_password']);
    unset($data['csrf_token']);
    // So we don't change usernames we unset the username from the data array if it's set
    if (isset($data['username'])) {
        unset($data['username']);
    }

    // If we are deleting the picutre, we need to remove it from the filesystem
    if (isset($data['picture'])) {
        if ($data['picture'] === '' || $data['picture'] === 'null' || $data['picture'] === null) {
            // Now let's find the current picture name
            $currentPicture = $user->get($userId)['picture'];
            $profilePicturePath = dirname($_SERVER['DOCUMENT_ROOT']) . '/public' . $currentPicture;
            // Now delete the file
            if (file_exists($profilePicturePath)) {
                unlink($profilePicturePath);
            } else {
                SystemLog::write('Could not delete the picture: ' . $profilePicturePath . '. Full payload was ' . json_encode($data), 'error');
            }
        }
    }

    $user->update($data, (int) $userId);
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // Let's check if the csrf token is passed as a query string in the DELETE request
    if (!isset($_GET['csrf_token'])) {
        Response::output('Missing CSRF Token', 401);
        exit();
    }

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        Response::output('Missing user id', 400);
        exit();
    }

    $userId = (int) $routeInfo[2]['id'];

    $checks = new Checks($vars, []);
    $checks->apiChecksDelete($_GET['csrf_token']);

    $user = new User();

    $user->delete($userId);
}
