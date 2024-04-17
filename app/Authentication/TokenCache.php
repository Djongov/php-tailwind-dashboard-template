<?php

namespace App\Authentication;

use App\Database\DB;
use App\Authentication\JWT;

class TokenCache
{
    // Check if there is a token in the cache for that username
    public static function exist(string $username) : bool
    {
        $db = new DB();
        $pdo = $db->getConnection();
        // Let's see if we have this tokem in the cache
        $stmt = $pdo->prepare("SELECT * FROM `cache` WHERE `type`='token' AND `unique_property`=?");
        $stmt->execute([$username]);
        $cached_token = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (!empty($cached_token)) ? true : false;
    }
    public static function get(string $username) : array
    {
        if (self::exist($username)) {
            $db = new DB();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM `cache` WHERE `type`='token' AND `unique_property`=?");
            $stmt->execute([$username]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    }
    public static function save(string $token) : void
    {
        if (!self::exist($token)) {
            $parsedToken = JWT::parseTokenPayLoad($token);
            // Set the expiration to the token's expiration but convert to mysql datetime
            $expiration = date('Y-m-d H:i:s', $parsedToken['exp']);
            $username = $parsedToken['email'] ?? $parsedToken['username'];
            $db = new DB();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("INSERT INTO `cache` (`value`, `type`,`unique_property`, `expiration`) VALUES (?, 'token', ?, ?)");
            $stmt->execute([$token, $username, $expiration]);
        }
    }
    public static function update(string $token, string $username) : void
    {
        $parsedToken = JWT::parseTokenPayLoad($token);
        // Set the expiration to the token's expiration but convert to mysql datetime
        $expiration = date('Y-m-d H:i:s', $parsedToken['exp']);
        $db = new DB();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("UPDATE `cache` SET `value`=?, `expiration`=? WHERE `type`='token' AND `unique_property`=?");
        $stmt->execute([$token, $expiration, $username]);
    }
}
