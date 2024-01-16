<?php

namespace Authentication;

use App\General;
use Api\Output;

class JWT
{
    // A method to generate a JWT token based off a private key and a set of claims
    public static function generateToken(array $claims) : string
    {
        // First let's make sure that the claims structure is correct
        
        // Required claims
        $requiredClaims = [
            'iss',
            'username',
            'name',
            'role',
            'last_ip'
        ];
        
        foreach ($requiredClaims as $claim) {
            if (!isset($claims[$claim])) {
                throw new \Exception('Missing required claim: ' . $claim . ' out of ' . implode(', ', $requiredClaims));
            }
        }
        // Expiration time
        $expiration = 3600;

        $claims['exp'] = time() + $expiration;

        $claims['nbf'] = time() - 1;

        // now iat
        $claims['iat'] = time();

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        // Base64url encode the header and payload
        $base64UrlHeader = General::base64url_encode(json_encode($header));
        $base64UrlPayload = General::base64url_encode(json_encode($claims));

        // Concatenate the base64url-encoded header and payload with a period
        $jwtUnsigned = $base64UrlHeader . '.' . $base64UrlPayload;

        // Sign the JWT with the private key
        $signature = '';
        openssl_sign($jwtUnsigned, $signature, base64_decode(JWT_PRIVATE_KEY), OPENSSL_ALGO_SHA256);

        // Base64url encode the signature
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Concatenate the JWT parts with periods
        $jwt = $jwtUnsigned . '.' . $base64UrlSignature;

        return $jwt;
    }
    // A method to validate a token that's been signed with our private key
    public static function validateToken(string $token) : bool
    {
        $jwtParts = explode('.', $token);

        if (count($jwtParts) !== 3) {
            // Invalid JWT format
            return false;
        }

        // Extract the header and payload from the JWT
        $base64UrlHeader = $jwtParts[0];
        $base64UrlPayload = $jwtParts[1];
        $signature = $jwtParts[2];

        // Decode the base64url-encoded header and payload
        $header = General::base64url_decode($base64UrlHeader);
        $payload = General::base64url_decode($base64UrlPayload);

        if (!$header || !$payload) {
            // Invalid header or payload
            return false;
        }

        // Concatenate the base64url-encoded header and payload with a period
        $jwtUnsigned = $base64UrlHeader . '.' . $base64UrlPayload;

        // Verify the signature using the public key, base64url decode the signature first
        $signatureToVerify = General::base64url_decode($signature);

        try {
            $verified = openssl_verify($jwtUnsigned, $signatureToVerify, base64_decode(JWT_PUBLIC_KEY), OPENSSL_ALGO_SHA256);

            if ($verified === 1) {
                // Signature is valid
                return true;
            } else {
                // An error occurred during verification
                Output::error('Error verifying signature');
            }
        } catch (\Exception $e) {
            // Handle the exception
            Output::error($e->getMessage());
        }
    }
    // A method to parse the payload of a token
    public static function parseTokenPayLoad(string $token) : array
    {
        $jwtParts = explode('.', $token);

        if (count($jwtParts) !== 3) {
            // Invalid JWT format
            return [];
        }

        // Extract the header and payload from the JWT
        $base64UrlPayload = $jwtParts[1];

        // Decode the base64url-encoded header and payload
        $payload = General::base64url_decode($base64UrlPayload);

        if (!$payload) {
            // Invalid header or payload
            return [];
        }

        return json_decode($payload, true);
    }
    // A method to check expiration of a token
    public static function checkExpiration(string $token) : bool
    {
        $payload = self::parseTokenPayLoad($token);

        if (isset($payload['exp'])) {
            if ($payload['exp'] < time()) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    // A method to check the combined validity of a token
    public static function checkToken(string $token) : bool
    {
        if (!self::validateToken($token)) {
            return false;
        }

        if (!self::checkExpiration($token)) {
            return false;
        }

        return true;
    }
    // This method will extract the username from the JWT token. The need and complexity of this method comes from the fact that we have different type of tokens, local and AzureAD
    public static function extractUserName(string $token) : string
    {
        $payload = self::parseTokenPayLoad($token);

        if (isset($payload['username'])) {
            return $payload['username'];
        } elseif (isset($payload['preferred_username'])) {
            return $payload['preferred_username'];
        } else {
            return '';
        }
    }
}
