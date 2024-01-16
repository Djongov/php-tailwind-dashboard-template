<?php

namespace Authentication;

use Api\Output;
use Authentication\JWT;

class Checks
{
    public function loginCheck(array $vars) : string|bool
    {
        $loggedIn = true;
        // Check $vars['loggedIn']
        if (!$vars['loggedIn']) {
            //Output::error('You are not logged in (loggedIn false');
            $loggedIn = false;
        }
        // Now check if the usernameArray is set
        if (!isset($vars['usernameArray'])) {
            //Output::error('You are not logged in (usernameArray not set');
            $loggedIn = false;
        }
        // Now check if the usernameArray is an array
        if (!is_array($vars['usernameArray'])) {
            //Output::error('You are not logged in (usernameArray not an array');
            $loggedIn = false;
        }
        // Now check if the usernameArray is not empty
        if (empty($vars['usernameArray'])) {
            //Output::error('You are not logged in (usernameArray empty');
            $loggedIn = false;
        }
        if (!$loggedIn) {
            header('Location: /login');
            exit();
        }
        return true;
    }
    public function usernameIntegrationCheck(array $vars) : string|bool
    {
        if (!isset($_COOKIE[AUTH_COOKIE_NAME])) {
            Output::error('Missing auth cookie');
        }

        $idToken = $_COOKIE[AUTH_COOKIE_NAME];

        // Check the token provider
        if (JWT::parseTokenPayLoad($idToken)['iss'] === $_SERVER['HTTP_HOST']) {
            $provider = 'local';
        } else {
            $provider = 'azure';
        }

        if ($provider === 'local') {
            if (!JWT::checkToken($idToken)) {
                Output::error('Invalid token');
            }
        } else {
            if (!AzureAD::check($idToken)) {
                Output::error('Invalid token');
            }
        }

        // Compare username from token to username from DB
        if ($vars['usernameArray']['username'] !== JWT::extractUserName($idToken)) {
            Output::error('Username anomaly');
        }

        return true;
    }
    public function checkCSRF($postToken) : string|bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            Output::error('Missing CSRF Token');
        }
        // Compare the postToken to the $_SESSION['csrf_token']
        if ($postToken !== $_SESSION['csrf_token']) {
            Output::error('Invalid CSRF Token');
        }
        return true;
    }
    public function checkCSRFHeader(string $postToken) : string|bool
    {
        // Pick up the X-CSRF-TOKEN header
        $headers = getallheaders();
        if (!isset($headers['X-Csrf-Token'])) {
            Output::error('Missing CSRF Token');
        }
        if ($postToken !== $headers['X-Csrf-Token']) {
            Output::error('Invalid CSRF Token');
        }
        return true;
    }
    public function checkSecretHeader() : string|bool
    {
        // get all headers
        $headers = getallheaders();
        // Check if the secret header is set
        if (!isset($headers[SECRET_HEADER])) {
            Output::error('Missing required header');
        }
        // Check if the secret header is correct
        if ($headers[SECRET_HEADER] !== SECRET_HEADER_VALUE) {
            Output::error('Invalid required header value');
        }
        return true;
    }
    public function genericChecks(array $vars) : string|bool
    {
        // Check if the user is logged in
        $this->loginCheck($vars);
        // Check if the usernameArray is integrated with the token
        $this->usernameIntegrationCheck($vars);
        return true;
    }
}