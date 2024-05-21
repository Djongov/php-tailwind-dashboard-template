<?php

namespace App\Authentication\Azure;

use Models\Core\DBCache;
use App\Authentication\JWT;

class GetAccessToken
{
    public static function dbGet($username)
    {
        $cachedToken = DBCache::get('access_token', $username);
        return ($cachedToken) ? $cachedToken : [];
    }
    public static function save($token, $username)
    {
        if (!self::dbGet($token)) {
            $parsedToken = JWT::parseTokenPayLoad($token);
            $expiration = date('Y-m-d H:i:s', $parsedToken['exp']);
            DBCache::create($token, $expiration, 'access_token', $username);
        }
    }
    public static function fetch()
    {
        // This will go to a special endpoint where the user will be asked to consent and get an access token after which it will be saved to the DB
        header('Location: /auth/azure-ad-access-token');
        exit();
    }
    public static function get()
    {
        $username = JWT::extractUserName($_COOKIE[AUTH_COOKIE_NAME]);
        $cachedToken = self::dbGet($username);
        if (!$cachedToken) {
            self::fetch();
        } else {
            return $cachedToken;
        }
    }
}
