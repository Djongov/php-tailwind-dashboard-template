<?php

namespace Models\Api;

use App\Database\DB;
use App\Logs\SystemLog;
use App\Exceptions\UserExceptions;

class User
{
    // Existence checks
    public function exists(string|int $param) : bool
    {
        // If the param is a string, we assume it's a username
        if (is_string($param)) {
            $query = 'SELECT `id` FROM `users` WHERE `username` = ?';
        } else {
            $query = 'SELECT `id` FROM `users` WHERE `id` = ?';
        }
        $db = new DB();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($query);
        $stmt->execute([$param]);
        return ($stmt->rowCount() > 0) ? true : false;
    }
    // User get
    public function get(string|int $param) : array
    {
        if (!$this->exists($param)) {
            throw (new UserExceptions)->userNotFound();
        } else {
            if (is_string($param)) {
                $column = 'username';
            } else {
                $column = 'id';
            }
            $db = new DB();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare('SELECT * FROM `users` WHERE `' . $column . '` = ?');
            $stmt->execute([$param]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    }
    // User Deleter
    public function delete(string|int $param) : bool
    {
        if (!$this->exists($param)) {
            throw (new UserExceptions)->userNotFound();
        } else {
            if (is_string($param)) {
                $column = 'username';
            } else {
                $column = 'id';
            }
            $db = new DB();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare('DELETE FROM `users` WHERE `' . $column . '` = ?');
            $stmt->execute([$param]);
            if ($stmt->rowCount() === 0) {
                throw (new UserExceptions)->userNotDeleted();
            } else {
                SystemLog::write('User with ' . $column . ' ' . $param . ' deleted', 'User API');
                return true;
            }
        }
    }
    // User updater
    public function update(array $data, int $id) : bool
    {
        // First let's check if the user exists
        if (!$this->exists($id)) {
            throw (new UserExceptions)->userNotFound();
        }
        $db = new DB();

        $db->checkDBColumnsAndTypes($data, 'users');

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

        $pdo = $db->getConnection();

        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);


        if ($stmt->rowCount() === 0) {
            throw (new UserExceptions)->userNotUpdated();
        } else {
            SystemLog::write('User with id ' . $id . ' updated with ' . json_encode($data), 'User API');
            return true;
        }
    }
    // User creator
    public function create(array $data) : bool
    {
        // First check if the user exists
        if ($this->exists($data['username'])) {
            throw (new UserExceptions)->userAlreadyExists();
        }
        $db = new DB();

        // Now let's check if the structure of the data matches the table
        $db->checkDBColumnsAndTypes($data, 'users');

        $sql = 'INSERT INTO `users` (';
        $columns = [];
        $values = [];
        foreach ($data as $key => $value) {
            $columns[] = "`$key`";
            $values[] = '?';
        }
        $sql .= implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ')';

        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));

        if ($stmt->rowCount() === 0){
            SystemLog::write('User not created with ' . json_encode($data), 'User API');
            throw (new UserExceptions)->userNotCreated();
        } else {
            SystemLog::write('User created with ' . json_encode($data), 'User API');
            return true;
        }
    }
}
