<?php declare(strict_types=1);

namespace App\Authentication;

use App\Authentication\JWT;
use Models\Core\DBCache;

class AccessTokenCache
{
    // Check if there is a token in the cache for that username
    public static function exist(string $username) : bool
    {
        $cachedToken = DBCache::get('access_token', $username);
        return ($cachedToken) ? true : false;
    }
    public static function get(string $username) : array
    {
        $cachedToken = DBCache::get('access_token', $username);
        return ($cachedToken) ? $cachedToken : [];
    }
    public static function save(string $token, string $username) : void
    {
        $parsedToken = JWT::parseTokenPayLoad($token);
        
        $expiration = date('Y-m-d H:i:s', $parsedToken['exp']);

        // If the username doesn't have an access token
        if (!self::exist($username)) {
            // if (isset($parsedToken['email'])) {
            //     $claimTokenUsername = $parsedToken['email'];
            // }
            // if (isset($parsedToken['username'])) {
            //     $claimTokenUsername = $parsedToken['username'];
            // }
            // if (isset($parsedToken['upn'])) {
            //     $claimTokenUsername = $parsedToken['upn'];
            // }
            DBCache::create($token, $expiration, 'access_token', $username);
        } else {
            // Check if expired
            self::update($token, $username);
        }
    }
    public static function update(string $token, string $username) : void
    {
        $parsedToken = JWT::parseTokenPayLoad($token);
        // Set the expiration to the token's expiration but convert to mysql datetime
        $expiration = date('Y-m-d H:i:s', $parsedToken['exp']);
        DBCache::update($token, $expiration, 'access_token', $username);
    }
}
