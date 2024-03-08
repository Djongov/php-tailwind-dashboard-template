<?php

namespace Models\Api;

use App\Database\MYSQL;
use App\Exceptions\FirewallException;

class Firewall
{
    private $table = 'firewall';
    private $mainColumn = 'ip_cidr';

    public function setter($table, $mainColumn) {
        $this->table = $table;
        $this->mainColumn = $mainColumn;
    }

    public function exists(string|int $param) : bool
    {
        // If the parameter is an integer, we assume it's an ID
        if (is_int($param)) {
            $result = MYSQL::queryPrepared("SELECT `$this->mainColumn` FROM `$this->table` WHERE `id` = ?", $param);
        } else {
            $result = MYSQL::queryPrepared("SELECT `$this->mainColumn` FROM `$this->table` WHERE `$this->mainColumn` = ?", $param);
        }
        return $result->num_rows === 1;
    }
    public function get(string $ip = '') : array
    {
        if ($ip === '') {
            $result = MYSQL::query("SELECT * FROM `$this->table`");
        } else {
            // Format the IP
            $ip = $this->formatIp($ip);
            // Check if IP exists
            if (!$this->exists($ip)) {
                throw (new FirewallException())->ipDoesNotExist();
            }
            $result = MYSQL::queryPrepared("SELECT * FROM `$this->table` WHERE `$this->mainColumn` = ?", $ip);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function save(string $ip, string $comment = '') : bool
    {
        // Format the IP
        $ip = $this->formatIp($ip);
        // Check if IP exists
        if ($this->exists($ip)) {
            throw (new FirewallException())->ipAlreadyExists();
        }
        $save = MYSQL::queryPrepared("INSERT INTO `$this->table` (`$this->mainColumn`, `comment`) VALUES (?,?)", [$ip, $comment]);
        if ($save->affected_rows === 1) {
            return true;
        } else {
            throw (new FirewallException())->notSaved('IP not saved');
        }
    }
    public function update(array $data, int $id) : bool
    {
        MYSQL::checkDBColumnsAndTypes($data, $this->table);

        if (!$this->exists($id)) {
            throw (new FirewallException())->ipDoesNotExist();
        }
        // Check if data is correct
        $sql = "UPDATE `$this->table` SET ";
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
        $update = MYSQL::queryPrepared($sql, $values);

        if ($update->affected_rows === 1) {
            return true;
        } else {
            throw (new FirewallException())->notSaved('IP not saved');
        }
    }
    public function delete(string $id) : bool
    {
        $delete = MYSQL::queryPrepared("DELETE FROM `$this->table` WHERE `id` = ?", $id);
        if ($delete->affected_rows === 1) {
            return true;
        } else {
            throw (new FirewallException())->notSaved('IP not deleted');
        }
    }
    public function validateIp(string $ip) : bool
    {
        $ipExplode = explode('/', $ip);
        $ip = $ipExplode[0];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw (new FirewallException())->invalidIP();
        }
        if (isset($ipExplode[1])) {
            $mask = $ipExplode[1];
            if ($mask < 0 || $mask > 32) {
                throw (new FirewallException())->invalidIP();
            }
        }
        return true;
    }
    public function formatIp(string $ip) : string
    {
        // First run through the validation
        $this->validateIp($ip);

        // Now let's format the IP to CIDR notation
        $ipExplode = explode('/', $ip);
        $ip = $ipExplode[0];
        if (!isset($ipExplode[1])) {
            $mask = 32;
        } else {
            $mask = $ipExplode[1];
        }
        return $ip . '/' . $mask;
    }
}
