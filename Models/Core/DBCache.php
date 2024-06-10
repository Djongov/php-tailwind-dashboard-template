<?php

namespace Models\Core;

use App\Database\DB;
use App\Logs\SystemLog;
use Controllers\Api\Output;

class DBCache implements DBCacheInterface
{
    private static string $table = 'cache';
    private static string $errorCategory = 'DBCache Error';

    public static function get(string $type, string $uniqueProperty) : array
    {
        $db = new DB();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM " . self::$table . " WHERE type=? AND unique_property=?");
        
        try {
            $stmt->execute([$type, $uniqueProperty]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("DBCache get error: " . $e->getMessage());
            SystemLog::write('DBCache get error: ' . $e->getMessage(), self::$errorCategory);
            return [];
        }
    }
    public static function create(mixed $value, string $expiration, string $type, string $uniqueProperty) : int
    {
        $db = new DB();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("INSERT INTO " . self::$table . " (value, expiration, type, unique_property) VALUES (?,?,?,?)");

        try {
            $stmt->execute([$value, $expiration, $type, $uniqueProperty]);
            return $pdo->lastInsertId();
        } catch (\PDOException $e) {
            SystemLog::write('DBCache create error: ' . $e->getMessage(), self::$errorCategory);
            return 0;
        }
    }
    public static function update(mixed $value, string $expiration, string $type, string $uniqueProperty) : int
    {
        $db = new DB();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("UPDATE " . self::$table . " SET value=?, expiration=? WHERE type=? AND unique_property=?");

        try {
            $stmt->execute([$value, $expiration, $type, $uniqueProperty]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            SystemLog::write('DBCache update error: ' . $e->getMessage(), self::$errorCategory);
        }
    }
    public static function delete(string $type, string $uniqueProperty) : int
    {
        $db = new DB();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("DELETE FROM " . self::$table . " WHERE type=? AND unique_property=?");

        try {
            $stmt->execute([$type, $uniqueProperty]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            SystemLog::write('DBCache delete error: ' . $e->getMessage(), self::$errorCategory);
        }
    }
}
