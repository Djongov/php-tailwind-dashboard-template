<?php

namespace Authentication;

use Request\Http;
use App\General;

class AzureAD
{
    public static function checkJWTToken(string $token): bool
    {
        // Explode the JWT token into header, paylod and signature
        $tokenParts = explode('.', $token);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];
        // Turn the JWT parts into arrays
        $header_array = json_decode($header, true);
        $payload_array = json_decode($payload, true);
        // If payload is empty unset auth cookie and return false
        if ($payload_array === null) {
            unset($_COOKIE[AUTH_COOKIE_NAME]);
            setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
            //writeToSystemLog('Incorrect or malformed token', 'JWT');
            return false;
        }
        // If audience does not match app registrations' client id unset auth cookie and return false
        if ($payload_array['aud'] !== Client_ID) {
            unset($_COOKIE[AUTH_COOKIE_NAME]);
            setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
            //writeToSystemLog('Incorrect Client ID', 'JWT');
            return false;
        }
        /* Disabling this as app is multi-tenant and the issuer is different based on the incoming tenant
        if ($payload_array['iss'] !== 'https://login.microsoftonline.com/' . $tenant . '/v2.0') {
            throw new Exception("Incorrect token issuer");
            return false;
        }
        if ($payload_array['tid'] !== $tenant) {
            throw new Exception("Incorrect tenant");
            return false;
        }
        */
        // Check if JWT token is valid
        if ($payload_array['nbf'] - time() > 0) {
            unset($_COOKIE[AUTH_COOKIE_NAME]);
            setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
            //writeToSystemLog('Token not yet valid', 'JWT');
            return false;
        }
        // Check the static nonce as well. This can be modified to a dynamic one with more functionality
        if ($payload_array['nonce'] !== 'c0ca2663770b3c9571ca843c7106851816e2d415e77369a1') {
            unset($_COOKIE[AUTH_COOKIE_NAME]);
            setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
            //writeToSystemLog('Incorrect nonce', 'JWT');
            return false;
        }

        // Signature check
        // 1 create array from token separated by dot (.)
        $token_arr = explode('.', $token);
        $headers_enc = $token_arr[0];
        $claims_enc = $token_arr[1];
        $sig_enc = $token_arr[2];

        // 2 base 64 url decoding
        $headers_arr = json_decode(General::base64url_decode($headers_enc), true);
        $claims_arr = json_decode(General::base64url_decode($claims_enc), true);
        $sig = General::base64url_decode($sig_enc);

        // Check if the kid is in the header. Maybe this check is not so important
        if (!isset($header_array['kid'])) {
            unset($_COOKIE[AUTH_COOKIE_NAME]);
            setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
            //writeToSystemLog('kid missing from JWT header', 'JWT');
            return false;
        }
        // get public key from key info
        $instance = new self();
        $get_signatures = $instance->getSignatures(Client_ID, Tenant_ID, $header_array["kid"]);
        if ($get_signatures === null || !isset($get_signatures['x5c'][0])) {
            unset($_COOKIE[AUTH_COOKIE_NAME]);
            setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
            //writeToSystemLog('x5c[0] not set', 'JWT');
            return false;
        }
        $cert_txt = '-----BEGIN CERTIFICATE-----' . "\n" . chunk_split($get_signatures['x5c'][0], 64) . '-----END CERTIFICATE-----';
        $cert_obj = openssl_x509_read($cert_txt);
        $pkey_obj = openssl_pkey_get_public($cert_obj);
        $pkey_arr = openssl_pkey_get_details($pkey_obj);
        $pkey_txt = $pkey_arr['key'];

        // 6 validate signature
        $token_valid = openssl_verify($headers_enc . '.' . $claims_enc, $sig, $pkey_txt, OPENSSL_ALGO_SHA256);

        if ($token_valid !== 1) {
            unset($_COOKIE[AUTH_COOKIE_NAME]);
            setcookie(AUTH_COOKIE_NAME, false, -1, '/', $_SERVER["HTTP_HOST"]);
            //writeToSystemLog('Incorrect Signature', 'JWT');
            return false;
        }

        if ($payload_array['exp'] - time() < 0) {
            header('Location:' . Login_Button_URL);
        }

        return true;
    }
    // This method will extract the username from the JWT token
    public static function extractUserName(string $token): ?string
    {
        $parsed_token = self::parseJWTTokenPayLoad($token);
        return $parsed_token['preferred_username'] ?? null;
    }


    protected function getSignatures(string $appId, string $tenant, string $header_kid): ?array
    {
        $url = "https://login.microsoftonline.com/$tenant/discovery/keys?appid=$appId";


        $request = new Http;

        $result = $request->get($url);

        $kid_array = [];

        if ($result !== null) {

            foreach ($result as $keys) {
                foreach ($keys as $props) {
                    if (in_array($header_kid, $props)) {
                        return $props;
                    }
                    array_push($kid_array, $props['kid']);
                }
            }
        } else {
            return null;
        }

        return $kid_array;
    }
    public static function parseJWTTokenPayLoad(string $jwt): ?array
    {
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) !== 3) {
            return null;
        }
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        $payload_array = json_decode($payload, true);
        return $payload_array;
    }
    public static function checkJWTTokenExpiry($jwt)
    {
        $payload_array = self::parseJWTTokenPayLoad($jwt);
        return ($payload_array['exp'] - time() < 0) ? false : true;
    }
}
