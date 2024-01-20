<?php

namespace Api;

use Api\Output;
use Authentication\JWT;
use Authentication\AzureAD;

class Checks
{
    /**
     * @var array
    */
    private $vars;
    /**
    * Checks constructor.
    *
    * @param array $vars
    */
    public function __construct($vars)
    {
        $this->vars = $vars;
    }
    /**
     * Checks if the user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->vars['isAdmin'] ?? false;
    }
    /**
     * Checks if the user is an admin from the JWT token
     *
     * @return bool
     */
    public function isAdminJWT(): bool
    {
        // Parse the JWT token
        $payload = JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME]);
        // Check if the roles are set
        if (!isset($payload['roles'])) {
            return false;
        }
        // Check if the user is an admin
        if (!is_array($payload['roles'])) {
            return false;
        }
        // Loop through the roles and check if the user is an admin
        foreach ($payload['roles'] as $role) {
            if ($role === 'administrator') {
                return true;
            }
        }
        return true;
    }
    /**
    * Performs the actual check if the user is admin on both DB and JWT token and outputs an error if not
    *
    * @return void
    */
    public function adminCheck(): void
    {
        if (!isset($this->vars['isAdmin'])) {
            Output::error('Administator status not set');
        }
        if (!$this->isAdmin()) {
            Output::error('This action requires admin privileges');
        }
        if (!$this->isAdminJWT()) {
            Output::error('This action requires admin privileges');
        }
    }
    /**
    * Checks if the JWT token is present and valid
    * - If the token is not present, it will output an error
    * - If the token is present but not valid, will strip the cookie and output an error
    */
    public function checkJWT(): void
    {
        if (!isset($_COOKIE[AUTH_COOKIE_NAME])) {
            Output::error('Missing token', 401);
        }
        if (!isset($this->vars['usernameArray']['provider'])) {
            Output::error('Missing provider in user');
        }
        if ($this->vars['usernameArray']['provider'] === 'local' && !JWT::checkToken($_COOKIE[AUTH_COOKIE_NAME])) {
            Output::error('Invalid local token', 401);
        }
        if ($this->vars['usernameArray']['provider'] === 'azure' && !AzureAD::check($_COOKIE[AUTH_COOKIE_NAME])) {
            Output::error('Invalid Azure token', 401);
        }
    }
    /**
    * Checks if the username is present and valid
    * - If the JWT token is not present, it will output an error
    * - If the username is not present, it will output an error
    * - If the username is present but not valid, will strip the cookie and output an error
    */
    public function checkUsernameIntegrity(): void
    {
        if (!isset($_COOKIE[AUTH_COOKIE_NAME])) {
            Output::error('Missing auth cookie');
        }
        if (!isset($this->vars['usernameArray']['username'])) {
            Output::error('Missing username');
        }
        if ($this->vars['usernameArray']['username'] !== JWT::extractUserName($_COOKIE[AUTH_COOKIE_NAME])) {
            JWT::handleValidationFailure();
            Output::error('Username anomaly');
        }
    }
    /**
    * CSRF POST param vs SESSION check for POST requests
    *
    * @return void
    */
    public function checkCSRF() : void
    {
        if (!isset($_SESSION['csrf_token'])) {
            Output::error('Missing CSRF Token');
        }
        if (!isset($_POST['csrf_token'])) {
            Output::error('Missing CSRF Token');
        }
        // Compare the postToken to the $_SESSION['csrf_token']
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            Output::error('Invalid CSRF Token');
        }
    }
    /**
    * CSRF header check for POST requests with header
    *
    * @return void
    */
    public function checkCSRFHeader() : void
    {
        // Pick up the X-CSRF-TOKEN header
        $headers = getallheaders();
        if (!isset($headers['X-Csrf-Token'])) {
            Output::error('Missing CSRF Token');
        }
        if (!isset($_POST['csrf_token'])) {
            Output::error('Missing CSRF Token');
        }
        if ($_POST['csrf_token'] !== $headers['X-Csrf-Token']) {
            Output::error('Invalid CSRF Token');
        }
    }
    /**
    * Login Checks
    *
    * @return void
    */
    public function loginCheck(): void
    {
        // Check if $vars['loggedIn'] is set
        if (!isset($this->vars['loggedIn'])) {
            Output::error('You are not logged in (loggedIn not set)');
        }
        // Check if $vars['loggedIn'] is true
        if (!$this->vars['loggedIn']) {
            Output::error('You are not logged in (loggedIn false)');
        }
        // Now check if the usernameArray is set
        if (!isset($this->vars['usernameArray'])) {
            Output::error('You are not logged in (usernameArray not set)');
        }
        // Now check if the usernameArray is an array
        if (!is_array($this->vars['usernameArray'])) {
            Output::error('You are not logged in (usernameArray not an array)');
        }
        // Now check if the usernameArray is not empty
        if (empty($this->vars['usernameArray'])) {
            Output::error('You are not logged in (usernameArray empty');
        }
    }
    public function checkSecretHeader(): void
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
    }
    /**
    * A complete set of checks, suitable for api calls
    *
    * @return void
    */
    public function apiAdminChecks(bool $checkSecretHeader = true): void
    {
        $this->adminCheck();
        $this->checkJWT();
        $this->checkUsernameIntegrity();
        $this->checkCSRF();
        $this->checkCSRFHeader();
        $this->loginCheck();
        if ($checkSecretHeader) {
            $this->checkSecretHeader();
        }
    }
    public function apiChecks(bool $checkSecretHeader = true): void
    {
        $this->checkJWT();
        $this->checkUsernameIntegrity();
        $this->checkCSRF();
        $this->checkCSRFHeader();
        $this->loginCheck();
        if ($checkSecretHeader) {
            $this->checkSecretHeader();
        }
    }
    /**
    * Checks if the required parameters are present
    *
    * @param array $allowedParams
    * @param array $providedParams
    *
    * @return void
    */
    public function checkParams(array $allowedParams, array $providedParams): void
    {
        foreach ($allowedParams as $name) {
            if (!array_key_exists($name, $providedParams)) {
                Output::error('missing parameter ' . $name, 400);
            }
            // need to check if the parameter is empty but not use empty() as it returns incorrect for value 0
            if ($providedParams[$name] === null || $providedParams[$name] === '') {
                Output::error('parameter ' . $name . ' cannot be empty', 400);
            }
        }
    }
}
