<?php

use App\Authentication\JWT;
use App\Authentication\AuthToken;

$state = $_GET['state'] ?? '/';

$username = JWT::extractUserName(AuthToken::get()) ?? die('No username found');

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

$location = AZURE_AD_OAUTH_URL . http_build_query($data);

header('Location: ' . $location);

exit();
