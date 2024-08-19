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
    public static function fetch($scope) : void
    {
        // This will go to a special endpoint where the user will be asked to consent and get an access token after which it will be saved to the DB
        $data = [
            'state' => $_SERVER['REQUEST_URI'],
            'username' => JWT::extractUserName(AuthToken::get()),
        ];
        if ($scope !== 'https://graph.microsoft.com/user.read') {
            $data['scope'] = $scope;
        }
        header('Location: /auth/azure/request-access-token?' . http_build_query($data));
        exit();
    }
    public static function get($scope = 'https://graph.microsoft.com/user.read') : string
    {
        // Find the username from the token
        $username = JWT::extractUserName(AuthToken::get());
        // Check the DB cache table for an entry with the username
        $cachedToken = self::dbGet($username);
        // If there is an entry, it will be in the value field
        if (isset($cachedToken['value'])) {
            // Let's parse the token payload
            $parsedToken = JWT::parseTokenPayLoad($cachedToken['value']);
            // Get the expiration date
            $expiration = $parsedToken['exp'] ?? $cachedToken['expiration']; // I need this because MS live tokens will not be decoded and we need to get the expiration from the DB
            // If the expiration is a string, convert it to a timestamp
            if (is_string($expiration)) {
                $expiration = strtotime($expiration);
            }
            // Now compare the expiration with the current time
            if ($expiration < time()) {
                // If expired, fetch a new token
                self::fetch($scope);
            } else {
                // If not expired, find out the scope of the token
                $tokenScope = self::getScope($cachedToken['value']);
                // so getScope returns the aud claim from the token but this function accepts the resource URL so we might have to extract the only up to path but excluding it
                $lastSlashPosition = strrpos($scope, '/');
                $resource = substr($scope, 0, $lastSlashPosition);
                // Now compare the scopes
                if ($tokenScope === $resource) {
                    // If the scopes match, return the token
                    return $cachedToken['value'];
                } else {
                    // First delete the existing token
                    DBCache::delete('access_token', $username);
                    // If the scopes don't match, fetch a new token
                    self::fetch($scope);
                }
                return $cachedToken['value'];
            }
        } else {
            self::fetch($scope);
        }
    }
    private static function getScope($token) : string
    {
        $parsedToken = JWT::parseTokenPayLoad($token);
        return $parsedToken['aud'] ?? '';
    }
}
