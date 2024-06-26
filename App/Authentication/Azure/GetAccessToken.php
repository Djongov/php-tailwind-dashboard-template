<?php declare(strict_types=1);

namespace App\Authentication\Azure;

use Models\Core\DBCache;
use App\Authentication\JWT;
use App\Authentication\AuthToken;

class GetAccessToken
{

    public static function dbGet($username) : array
    {
        $cachedToken = DBCache::get('access_token', $username);
        return ($cachedToken) ? $cachedToken : [];
    }
    public static function save($token, $username) : void
    {
        if (!self::dbGet($token)) {
            $parsedToken = JWT::parseTokenPayLoad($token);
            $expiration = date('Y-m-d H:i:s', $parsedToken['exp']);
            DBCache::create($token, $expiration, 'access_token', $username);
        }
    }
    public static function fetch() : void
    {
        // This will go to a special endpoint where the user will be asked to consent and get an access token after which it will be saved to the DB
        //header('Location: /auth/azure-ad-access-token?username=' . JWT::extractUserName(AuthToken::get()));
        header('Location: /auth/azure/request-access-token?state=' . $_SERVER['REQUEST_URI'] . '&username=' . JWT::extractUserName(AuthToken::get()));
        exit();
    }
    public static function get() : string
    {
        $username = JWT::extractUserName(AuthToken::get());
        $cachedToken = self::dbGet($username);
        // Let's find out if the token is expired
        if (isset($cachedToken['value'])) {
            $parsedToken = JWT::parseTokenPayLoad($cachedToken['value']);
            $expiration = $parsedToken['exp'] ?? $cachedToken['expiration']; // I need this because MS live tokens will not be decoded and we need to get the expiration from the DB
            if (is_string($expiration)) {
                $expiration = strtotime($expiration);
            }
            // If the token is expired we need to fetch a new one
            if ($expiration < time()) {
                self::fetch();
            } else {
                return $cachedToken['value'];
            }
        } else {
            self::fetch();
        }
    }
}
