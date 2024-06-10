<?php
// This is the model of the User Api
namespace Controllers\Api;

use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\Utilities\IP;
use Models\Api\User as UserModel;
use App\Exceptions\UserExceptions;

class User
{
    public function get(string|int $username = null) : array
    {
        $user = new UserModel();
        try {
            return $user->get($username);
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: UserExceptions getting user', 'unable to get username');
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: Exception getting user', 'unable to get username');
        }
    }
    public function create(array $data, string $provider) : void
    {
        if (!in_array($provider, SUPPORTED_AUTH_PROVIDERS)) {
            Output::error('Provider not supported. Must be on of ' . implode(', ', SUPPORTED_AUTH_PROVIDERS), 400);
        }
        if ($provider === 'azure') {
            $this->createAzureUser($data);
        } elseif ($provider === 'mslive') {
            $this->createMsLiveUser($data);
        } elseif ($provider === 'google') {
            $this->createGoogleUser($data);
        } elseif ($provider === 'local') {
            $this->createLocalUser($data);
        }
    }
    public function createAzureUser(array $data) : void
    {
        // $data is the contents of the JWT token, so we need to do some transformations before we can use it
        $insertData = [];
        // usernames comes as preferred_username in the JWT token
        $insertData['username'] = $data['preferred_username'] ?? Output::error('Missing preferred_username in token', 400);
        // Prepare the email, if it's not present in the JWT token, use the username
        $insertData['email'] = $data['email'] ?? $insertData['username'];
        // Name comes as name in the JWT token
        $insertData['name'] = $data['name'] ?? Output::error('Missing name in token', 400);
        // Last IPs comes as ipaddr in the JWT token, if it's not present, use the current IP
        $insertData['last_ips'] = $data['ipaddr'] ?? IP::currentIP();
        // If JWT has a claim called 'ctry' take it, otherwise take the browser language
        $insertData['origin_country'] = (isset($data['ctry'])) ? $data['ctry'] : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        // Role comes as an array of roles in the JWT token, we only need the first one
        $insertData['role'] = $data['roles'][0] ?? 'user';
        // Theme is set to the default color scheme
        $insertData['theme'] = COLOR_SCHEME;
        $insertData['provider'] = 'azure';
        $insertData['enabled'] = 1;

        $allowedData = ['username', 'email', 'name', 'last_ips', 'origin_country', 'role', 'theme', 'provider', 'enabled'];

        $checks = new Checks($allowedData, $insertData);

        $checks->checkParams($allowedData, $insertData);

        try {
            $createUser = new UserModel();
            $createUser->create($insertData);
            echo Output::success('User created');
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: UserExceptions creating azure provider user', 'unable to create user');
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: Exception creating azure provider user', 'unable to create user');
        }
    }
    public function createMsLiveUser(array $data)
    {
        // $data is the contents of the JWT token, so we need to do some transformations before we can use it
        $insertData = [];
        // usernames comes as preferred_username in the JWT token
        $insertData['username'] = $data['preferred_username'] ?? Output::error('Missing preferred_username in token', 400);
        // Prepare the email, if it's not present in the JWT token, use the username
        $insertData['email'] = $data['email'] ?? $insertData['username'];
        // Name comes as name in the JWT token
        $insertData['name'] = $data['name'] ?? Output::error('Missing name in token', 400);
        // Last IPs comes as ipaddr in the JWT token, if it's not present, use the current IP
        $insertData['last_ips'] = IP::currentIP();
        // If JWT has a claim called 'ctry' take it, otherwise take the browser language
        $insertData['origin_country'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        // Role comes as an array of roles in the JWT token, we only need the first one
        $insertData['role'] = 'user'; // There are no roles in live id tokens
        // Theme is set to the default color scheme
        $insertData['theme'] = COLOR_SCHEME;
        $insertData['provider'] = 'mslive';
        $insertData['enabled'] = 1;

        $allowedData = ['username', 'email', 'name', 'last_ips', 'origin_country', 'role', 'theme', 'provider', 'enabled'];

        $checks = new Checks($allowedData, $insertData);

        $checks->checkParams($allowedData, $insertData);

        try {
            $createUser = new UserModel();
            $createUser->create($insertData);
            echo Output::success('User created');
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: UserExceptions creating mslive provider user', 'unable to create user');
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: Exception creating mslive provider user', 'unable to create user');
        }
    }
    public function createGoogleUser(array $data)
    {
        // $data is the contents of the JWT token, so we need to do some transformations before we can use it
        $insertData = [];
        // usernames comes as preferred_username in the JWT token
        $insertData['username'] = $data['email'] ?? Output::error('Missing email in token', 400);
        // Prepare the email, if it's not present in the JWT token, use the username
        $insertData['email'] = $data['email'];
        // Name comes as name in the JWT token
        $insertData['name'] = $data['name'] ?? Output::error('Missing name in token', 400);
        // Last IPs comes as ipaddr in the JWT token, if it's not present, use the current IP
        $insertData['last_ips'] = IP::currentIP();
        // If JWT has a claim called 'ctry' take it, otherwise take the browser language
        $insertData['origin_country'] = (isset($data['locale'])) ? $data['locale'] : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        // Role comes as an array of roles in the JWT token, we only need the first one
        $insertData['role'] = $data['roles'][0] ?? 'user';
        // Theme is set to the default color scheme
        $insertData['theme'] = COLOR_SCHEME;
        $insertData['picture'] = $data['picture'] ?? null;
        $insertData['provider'] = 'google';
        $insertData['enabled'] = 1;

        $allowedData = ['username', 'email', 'name', 'last_ips', 'origin_country', 'role', 'theme', 'provider', 'enabled'];

        $checks = new Checks($allowedData, $insertData);

        $checks->checkParams($allowedData, $insertData);

        try {
            $createUser = new UserModel();
            $createUser->create($insertData);
            echo Output::success('User created');
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: UserExceptions creating google provider user', 'unable to create user');
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: Exception creating google provider user', 'unable to create user');
        }
    }
    public function createLocalUser(array $data) : void
    {
        if (!isset($data['email'])) {
            $data['email'] = $data['username'];
        }

        $allowedData = ['username', 'password', 'confirm_password', 'email', 'name', 'last_ips', 'origin_country', 'role', 'theme', 'provider', 'enabled'];

        $checks = new Checks($allowedData, $data);

        $checks->checkParams($allowedData, $data);


        if ($data['password'] !== $data['confirm_password']) {
            Output::error('Passwords do not match', 400);
        }

        unset($data['confirm_password']);

        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $createUser = new UserModel();
            $createUser->create($data);
            echo Output::success('User created');
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: UserExceptions creating local provider user', $e->getMessage());
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: Exception creating local provider user', $e->getMessage());
        }
    }
    public function update(array $data, int $id) : void
    {
        $user = new UserModel();
        try {
            $user->update($data, $id);
            echo Output::success('User updated');
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: UserExceptions updating user with id ' . $id . ' and data ' . json_encode($data), 'unable to update user');
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: Exception updating user with id ' . $id . ' and data ' . json_encode($data), 'unable to update user');
        }
    }
    public function delete(int $id) : void
    {
        $user = new UserModel();
        try {
            $user->delete($id);
            echo Output::success('User deleted');
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: UserExceptions deleting user with id ' . $id, 'unable to delete user');
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: Exception deleting user with id ' . $id, 'unable to delete user');
        }
    }
    public function updateLastLogin(string $username) : void
    {
        // First check if the user exists
        $updatedUser = new UserModel();
        $updatedUserArray = $updatedUser->get($username);

        try {
            $updatedUser->update(['last_login' => date('Y-m-d H:i:s')], $updatedUserArray['id']);
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: UserExceptions updating last login for username ' . $username, 'unable to update last login');
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: Exception updating last login for username ' . $username, 'unable to update last login');
        }
    }
    public function saveAzureProfilePicture(string $username, string $response) : void
    {
        $imageName = $username . '_' . uniqid() . '.jpeg';
        $imageRelativePath = '/assets/images/profile/' . $imageName;
        $savePath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/profile/' . $imageName;

        file_put_contents($savePath, $response);

        $user = new UserModel();

        try {
            $usernameConfirmed = $user->get($username);
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: UserExceptions getting user for saving azure profile picture', 'unable to get username');
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: Exception getting user for saving azure profile picture', 'unable to get username');
        }

        try {
            $user->update(['picture' => $imageRelativePath], $usernameConfirmed['id']);
        } catch (UserExceptions $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: error saving azure profile picture', 'unable to save profile picture');
        } catch (\Exception $e) {
            $this->handleUserErrorsApiResponse($e, 'user controller: error saving azure profile picture', 'unable to save profile picture');
        }
    }
    private function handleUserErrorsApiResponse(\Exception $e, string $verboseError, string $publicError) : void
    {
        if (ERROR_VERBOSE) {
            Output::error($verboseError . '. Error: ' . $e->getMessage());
        } else {
            Output::error($publicError, 404);
        }
    }
}
