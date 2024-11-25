<?php declare(strict_types=1);

namespace App\Api;

use App\Api\Response;
use App\Authentication\JWT;
use App\Authentication\Azure\AzureAD;
use App\Authentication\AuthToken;

class Checks
{
    /**
     * @var array
     */
    private array $userVars;
    private array $data;
    /**
     * Checks constructor.
     *
     * @param array $vars
     */
    public function __construct(array $userVars, array $data)
    {
        $this->userVars = $userVars;
        $this->data = $data;
    }
    /**
     * Checks if the user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->userVars['isAdmin'] ?? false;
    }
    /**
     * Checks if the user is an admin from the JWT token
     *
     * @return bool
     */
    public function isAdminJWT(): bool
    {
        // Parse the JWT token
        $payload = JWT::parseTokenPayLoad(AuthToken::get());
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
        if (!isset($this->userVars['isAdmin'])) {
            Response::output('Administator status not set');
        }
        if (!$this->isAdmin()) {
            Response::output('This action requires admin privileges');
        }
        if (!$this->isAdminJWT()) {
            Response::output('This action requires admin privileges coming from JWT token');
        }
    }
    /**
     * Checks if the JWT token is present and valid
     * - If the token is not present, it will output an error
     * - If the token is present but not valid, will strip the cookie and output an error
     */
    public function checkJWT(): void
    {
        if (AuthToken::get() === null) {
            Response::output('Missing token', 401);
        }
        if (!isset($this->userVars['usernameArray']['provider'])) {
            Response::output('Missing provider in user');
        }
        if ($this->userVars['usernameArray']['provider'] === 'local' && !JWT::checkToken(AuthToken::get())) {
            Response::output('Invalid local token', 401);
        }
        if ($this->userVars['usernameArray']['provider'] === 'azure' && !AzureAD::check(AuthToken::get())) {
            Response::output('Invalid Azure token', 401);
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
        if (AuthToken::get() === null) {
            Response::output('Missing auth token');
        }
        if (!isset($this->userVars['usernameArray']['username'])) {
            Response::output('Missing username');
        }
        if ($this->userVars['usernameArray']['username'] !== JWT::extractUserName(AuthToken::get())) {
            JWT::handleValidationFailure();
            Response::output('Username anomaly');
        }
    }
    /**
     * CSRF POST param vs SESSION check for POST requests
     *
     * @return void
     */
    public function checkCSRF(): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            Response::output('Missing Session CSRF Token');
        }
        if (!isset($this->data['csrf_token'])) {
            Response::output('Missing POST CSRF Token');
        }
        // Compare the postToken to the $_SESSION['csrf_token']
        if ($this->data['csrf_token'] !== $_SESSION['csrf_token']) {
            Response::output('Invalid CSRF Token');
        }
    }
    /**
     * CSRF header check for POST requests with header
     *
     * @return void
     */
    public function checkCSRFHeader(): void
    {
        // Pick up the X-CSRF-TOKEN header
        $headers = getallheaders();
        $lowercaseHeaders = array_change_key_case($headers, CASE_LOWER);
        if (!isset($lowercaseHeaders['x-csrf-token'])) {
            Response::output('Missing CSRF Token header');
        }
        if (!isset($this->data['csrf_token'])) {
            Response::output('Missing POST CSRF Token');
        }
        if ($this->data['csrf_token'] !== $lowercaseHeaders['x-csrf-token']) {
            Response::output('Invalid CSRF Token');
        }
    }
    public function checkCSRFDelete(string $csrf): void
    {
        // $csrf should come from the URL
        if (!isset($_SESSION['csrf_token'])) {
            Response::output('Missing Session CSRF Token');
        }
        if ($csrf !== $_SESSION['csrf_token']) {
            Response::output('Invalid CSRF Token');
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
        if (!isset($this->userVars['loggedIn'])) {
            Response::output('You are not logged in (loggedIn not set)');
        }
        // Check if $vars['loggedIn'] is true
        if (!$this->userVars['loggedIn']) {
            Response::output('You are not logged in (loggedIn false)');
        }
        // Now check if the usernameArray is set
        if (!isset($this->userVars['usernameArray'])) {
            Response::output('You are not logged in (usernameArray not set)');
        }
        // Now check if the usernameArray is an array
        if (!is_array($this->userVars['usernameArray'])) {
            Response::output('You are not logged in (usernameArray not an array)');
        }
        // Now check if the usernameArray is not empty
        if (empty($this->userVars['usernameArray'])) {
            Response::output('You are not logged in (usernameArray empty)');
        }
    }
    public function checkSecretHeader(): void
    {
        // get all headers
        $headers = getallheaders();
        $lowercaseHeaders = array_change_key_case($headers, CASE_LOWER);
        // Check if the secret header is set
        if (!isset($lowercaseHeaders[SECRET_HEADER])) {
            Response::output('Missing required header');
        }
        // Check if the secret header is correct
        if ($lowercaseHeaders[SECRET_HEADER] !== SECRET_HEADER_VALUE) {
            Response::output('Invalid required header value');
        }
    }
    public function checkImage(array $image): void
    {
        // Check if the image is set
        if (!isset($image['name'])) {
            Response::output('Missing image');
        }
        // Check if the image is an image
        if (!getimagesize($image['tmp_name'])) {
            Response::output('Invalid image');
        }
        // Check if the image is not too big
        if ($image['size'] > 1000000) {
            Response::output('Image too big');
        }
        // Check if the image is the proper format
        $imageFileType = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'affif'])) {
            Response::output('Invalid image type');
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
    public function apiChecksNoUser(bool $checkSecretHeader = true): void
    {
        $this->checkCSRF();
        $this->checkCSRFHeader();
        if ($checkSecretHeader) {
            $this->checkSecretHeader();
        }
    }
    public function apiChecksNoCSRF(bool $checkSecretHeader = true): void
    {
        $this->checkJWT();
        $this->checkUsernameIntegrity();
        $this->loginCheck();
        if ($checkSecretHeader) {
            $this->checkSecretHeader();
        }
    }
    public function apiChecksNoCSRFHeader(bool $checkSecretHeader = true): void
    {
        $this->checkJWT();
        $this->checkUsernameIntegrity();
        $this->checkCSRF();
        $this->loginCheck();
        if ($checkSecretHeader) {
            $this->checkSecretHeader();
        }
    }
    // Because DELETE requests don't have a body
    public function apiChecksDelete(string $csrf, bool $checkSecretHeader = true): void
    {
        $this->checkJWT();
        $this->checkUsernameIntegrity();
        $this->loginCheck();
        if ($checkSecretHeader) {
            $this->checkSecretHeader();
        }
        $this->checkCSRFDelete($csrf);
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
                Response::output('missing parameter ' . $name, 400);
            }
            // need to check if the parameter is empty but not use empty() as it returns incorrect for value 0
            if ($providedParams[$name] === null || $providedParams[$name] === '') {
                Response::output('parameter ' . $name . ' cannot be empty', 400);
            }
        }
    }
    public static function jsonBody(): array
    {
        // Let's catch php input stream
        $putData = file_get_contents('php://input');
        // Now we need to get the put data and make into array
        $putData = json_decode($putData, true);
        if (!is_array($putData)) {
            Response::output('Invalid json data', 400);
        }
        return $putData;
    }
}
