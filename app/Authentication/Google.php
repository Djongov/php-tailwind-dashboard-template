<?php

namespace Authentication;

use Request\HttpClient;
use Authentication\JWT;
use Api\Output;
use App\General;
use Google\Client;
use Authentication\X5CHandler;

class Google
{
    public static function getSignatures(string $kid) : array
    {
        // Fetch the x5c from Google
        $client = new HttpClient('https://www.googleapis.com/oauth2/v3/certs');

        $response = $client->call('GET', '', [], null, false, ['User-Agent' => 'dashboardTemplate/1.0']);

        // We expect the response to have a keys array

        if (!isset($response['keys'])) {
            return [];
        }

        // Now let's search for the kid
        foreach ($response['keys'] as $key) {
            if ($key['kid'] === $kid) {
                // Return the entire key
                return $key;
            }
        }
    }
    public static function verifyIdToken(string $idToken)
    {
        $idTokenArray = JWT::parse($idToken);

        $header = $idTokenArray[0];
        $payload = $idTokenArray[1];
        $base64UrlSignature = $idTokenArray[2];

        // Check the claims we need and if they are present
        $expectedClaims = ['email', 'name', 'exp', 'iat', 'iss', 'aud'];

        // There are more than the expected claims, let's check if the expected claims are present
        if (count(array_intersect($expectedClaims, array_keys($payload))) !== count($expectedClaims)) {
            return false;
        }

        // First check the issuer
        if ($payload['iss'] !== 'https://accounts.google.com') {
            return false;
        }

        // Now check the audience
        if ($payload['aud'] !== GOOGLE_CLIENT_ID) {
            return false;
        }

        // Now check the expiration
        if ($payload['exp'] < time()) {
            return false;
        }

        $client = new Client(['client_id' => GOOGLE_CLIENT_ID]);
        $client->setHttpClient(new \GuzzleHttp\Client(['verify' => CURL_CERT, 'timeout' => 60, 'http_errors' => false]));
        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
            return false;
        }

        // // Now check the signature
        // //$x5c = self::getSignatures($header['kid']);
        // $x5c = X5CHandler::load(GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, $header['kid'], 'google');
        // // We've saved the n and e in the x5c value but with a space in between
        // $x5cArray = explode(" ", $x5c);

        // if (!isset($x5cArray[0], $x5cArray[1])) {
        //     return false;
        // }

        // $modulus = base64_decode($x5cArray[0]);
        // $exponent = base64_decode($x5cArray[1]);

        // // Construct RSA public key in DER format
        // $der = "305C300D06092A864886F70D0101010500034B003048024100" . bin2hex($modulus) . "0203" . bin2hex($exponent);

        // // Convert DER to PEM
        // $pem = "-----BEGIN RSA PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END RSA PUBLIC KEY-----\n";

        // // Convert the cert to OpenSSL format
        // $pkey = openssl_pkey_get_public($pem);

        // $signature = General::base64url_decode($base64UrlSignature);

        // // Explode the token
        // $jwtParts = explode('.', $idToken);


        // $verified = openssl_verify($jwtParts[0] . '.' . $jwtParts[1], $signature, $pkey, OPENSSL_ALGO_SHA256);

        // var_dump($verified);

        // if ($verified !== 1) {
        //     // Invalid signature
        //     return false;
        // }

        // Signature is valid
        return true;
    }
    public static function check(string $idToken)
    {
        $expiration = JWT::checkExpiration($idToken);
        if (!$expiration) {
            Output::error('Token expired', 400);
            return false;
        }
        $check = self::verifyIdToken($idToken);
        if (!$check) {
            Output::error('Invalid token', 400);
            return false;
        }
        return true;
    }
}
