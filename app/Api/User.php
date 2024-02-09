<?php
// This is the model of the User Api
namespace Api;

use Api\Output;
use Api\Checks;
use Database\MYSQL;
use App\General;

class User
{
    // Getter
    public function get(string $id) : array
    {
        $user = MYSQL::queryPrepared('SELECT * FROM `users` WHERE `id`=?', [$id]);
        if ($user->num_rows === 0) {
            Output::error('User not found', 404);
        }
        return $user->fetch_assoc();
    }
    public function create(array $data, string $provider) : void
    {
        if ($provider === 'local') {
            $this->createLocalUser($data);
        } elseif ($provider === 'azure') {
            $this->createAzureUser($data);
        } else {
            Output::error('Invalid provider', 400);
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
        $insertData['last_ips'] = $data['ipaddr'] ?? General::currentIP();
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

        MYSQL::checkDBColumnsAndTypes($insertData, 'users');

        if ($this->existByUsername($insertData['username'])) {
            Output::error('Username already taken', 409);
        }

        $createUser = MYSQL::queryPrepared('INSERT INTO `users`(`username`, `email`, `name`, `last_ips`, `origin_country`, `role`, `last_login`, `theme`, `provider`, `enabled`) VALUES (?,?,?,?,?,?,NOW(),?,?,?)', array_values($insertData));

        if ($createUser->affected_rows === 1) {
            echo Output::success('User created');
        } else {
            Output::error('User creation failed', 400);
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

        MYSQL::checkDBColumnsAndTypes($data, 'users');

        // if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        //     Output::error('Invalid email', 400);
        // }


        if ($this->existByUsername($data['username'])) {
            Output::error('Username already taken', 409);
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $createUser = MYSQL::queryPrepared('INSERT INTO `users`(`username`, `password`, `email`, `name`, `last_ips`, `origin_country`, `role`, `last_login`, `theme`, `provider`, `enabled`) VALUES (?,?,?,?,?,?,?,NOW(),?,"local",?)',
        [
            $data['username'], // username
            $passwordHash, // password
            $data['email'], // email
            $data['name'], // name
            $data['last_ips'], // last_ips
            $data['origin_country'], // origin_country
            $data['role'], // role
            $data['theme'], // theme
            $data['enabled'] // enabled
        ]);

        if ($createUser->affected_rows === 1) {
            echo Output::success('User created');
        } else {
            Output::error('User creation failed', 400);
        }
    }
    public function existByUsername(string $username) : bool
    {
        $user = MYSQL::queryPrepared('SELECT * FROM `users` WHERE `username`=?', [$username]);
        return ($user->num_rows > 0) ? true : false;
    }
    public function existById(string $id) : bool
    {
        $user = MYSQL::queryPrepared('SELECT * FROM `users` WHERE `id`=?', [$id]);
        return ($user->num_rows > 0) ? true : false;
    }
    public function update(array $data, string $id)
    {
        if (!$this->existById($id)) {
            Output::error('User not found', 404);
        }

        MYSQL::checkDBColumnsAndTypes($data, 'users');

        $sql = 'UPDATE `users` SET ';
        $updates = [];
        // Check if all keys in $array match the columns
        foreach ($data as $key => $value) {
            // Add the column to be updated to the SET clause
            $updates[] = "`$key` = ?";
        }
        // Combine the SET clauses with commas
        $sql .= implode(', ', $updates);

        // Add a WHERE clause to specify which organization to update
        $sql .= " WHERE `id` = ?";

        // Prepare and execute the query using queryPrepared
        $values = array_values($data);
        $values[] = $id; // Add the id for the WHERE clause

        $update_user = MYSQL::queryPrepared($sql, $values);

        if ($update_user->affected_rows === 0) {
            echo Output::error('Nothing updated', 409);
        } else {
            echo Output::success('User updated');
        }

    }
    public function recordLastLogin(string $username) : bool
    {
        $lastIp = General::currentIP();
        $updateUser = MYSQL::queryPrepared('UPDATE `users` SET `last_ips`=?, `last_login`=NOW() WHERE `username`=?', [$lastIp, $username]);
        return ($updateUser->affected_rows === 1) ? true : false;
    }
}
