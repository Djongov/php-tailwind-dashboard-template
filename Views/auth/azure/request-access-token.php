<?php declare(strict_types=1);

use App\Authentication\JWT;
use App\Authentication\AuthToken;
use Controllers\Api\Output;
use App\Request\HttpClient;

$state = $_GET['state'] ?? '/';

$username = JWT::extractUserName(AuthToken::get()) ?? die('No username found');

$scope = (isset($_GET['scope'])) ? $_GET['scope'] : 'https://graph.microsoft.com/user.read';

// Function to check if the user has already consented
function attemptSilentTokenRequest(array $data, string $token_url): bool {
    $client = new HttpClient($token_url);

    $response = $client->call('POST', '', $data, null, false, [], true);

    // Check if an access token was returned
    return isset($response['access_token']);
}

// Prepare the common data array
$data = [
    'client_id' => AZURE_AD_CLIENT_ID,
    'response_type' => 'code',
    'redirect_uri' => AZURE_AD_CODE_REDIRECT_URI,
    'scope' => $scope,
    'response_mode' => 'form_post',
    'state' => $state . '&username=' . $username,
    'login_hint' => $username,
    'prompt' => 'none' // Attempt silent authentication first
];

if ($usernameArray['provider'] === 'azure') {
    $url = AZURE_AD_OAUTH_URL;
} elseif ($usernameArray['provider'] === 'mslive') {
    $url = 'https://login.microsoftonline.com/consumers/oauth2/v2.0/authorize';
} else {
    Output::error('Invalid provider');
    exit();
}

// Check if user has already consented
$token_request_data = [
    'client_id' => $data['client_id'],
    'grant_type' => 'authorization_code',
    'redirect_uri' => $data['redirect_uri'],
    'code' => $_GET['code'] ?? '',
    'client_secret' => AZURE_AD_CLIENT_SECRET, // Make sure this is defined in your config
    'scope' => $data['scope']
];

$token_url = str_replace('/authorize', '/token', $url);

if (!attemptSilentTokenRequest($token_request_data, $token_url)) {
    // User hasn't consented, modify data to force consent
    $data['prompt'] = 'consent';
}

// Build the authorization URL
$location = $url . http_build_query($data);

// Redirect the user
header('Location: ' . $location);
exit();
