<?php

use App\Authentication\JWT;
use App\Authentication\AuthToken;
use Controllers\Api\Output;

$state = $_GET['state'] ?? '/';

$username = JWT::extractUserName(AuthToken::get()) ?? die('No username found');

// if (!isset($_GET['provider'])) {
//     Output::error('No provider specified');
// }

// if ($_GET['provider'] === 'mslive') {
//     $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?';
// } elseif ($_GET['provider'] === 'azure') {
//     $url = AZURE_AD_OAUTH_URL;
// } else {
//     Output::error('Invalid provider');
// }

$data = [
    'client_id' => AZURE_AD_CLIENT_ID,
    'response_type' => 'token',
    'redirect_uri' => AZURE_AD_CODE_REDIRECT_URI,
    'scope' => 'https://graph.microsoft.com/user.read',
    'response_mode' => 'form_post',
    'state' => $state . '&username=' . $username,
    'nonce' => $_SESSION['nonce'],
    'prompt' => 'none',
    'login_hint' => $username
];

$location = 'https://login.microsoftonline.com/consumers/oauth2/v2.0/authorize?' . http_build_query($data);

return var_dump($location);

header('Location: ' . $location);

exit();
