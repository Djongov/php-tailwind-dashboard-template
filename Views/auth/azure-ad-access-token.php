<?php

declare(strict_types=1);

use Controllers\Api\Output;
use App\Authentication\JWT;

if (isset($_POST['error'], $_POST['error_description'])) {
    if (str_contains($_POST['error'], 'consent_required')) {
        // Send an Authorization request if the error is AADSTS65001 (consent_required)
        $authorizationUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';

        $data = [
            'client_id' => AZURE_AD_CLIENT_ID,
            'response_type' => 'code',
            'redirect_uri' => $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] , // redirect back to the same page
            'scope' => 'https://graph.microsoft.com/user.read',
            'response_mode' => 'form_post',
            'state' => $_POST['state'],
            'nonce' => $_SESSION['nonce'],
            'prompt' => 'consent',
            'login_hint' => JWT::extractUserName($_COOKIE[AUTH_COOKIE_NAME])
        ];

        header('Location: ' . $authorizationUrl . http_build_query($data));
        exit();
    }
    Output::error("Azure Error: " . $_POST['error'] . " with Description: " . $_POST['error_description'], 400);
}


if (isset($_POST['code'], $_POST['state'], $_POST['session_state'])) {
    $code = $_POST['code'];

    $tokenUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    $postData = [
        'grant_type' => 'authorization_code',
        'client_id' => AZURE_AD_CLIENT_ID,
        'client_secret' => AZURE_AD_CLIENT_SECRET,
        'code' => $code,
        'redirect_uri' => REDIRECT_URI
    ];

    $client = new App\Request\HttpClient($tokenUrl);

    $request = $client->call('POST', '', $postData);
}

// And finally arriving with the token

if (isset($_POST['access_token'], $_POST['token_type'], $_POST['expires_in'], $_POST['scope'], $_POST['state'], $_POST['session_state'])) {
    App\Authentication\AccessTokenCache::save($_POST['access_token']);
    header('Location: /');
}
// Let's do some checks on the token to handle data structure we expect
header('Location: https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=' . AZURE_AD_CLIENT_ID . '&response_type=token&redirect_uri=' . urlencode($protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '&scope=https%3A%2F%2Fgraph.microsoft.com%2Fuser.read&response_mode=form_post&state=' . urlencode($destination) . '&nonce=' . $_SESSION['nonce'] . '&prompt=none&login_hint=' . urlencode(JWT::extractUserName($_COOKIE[AUTH_COOKIE_NAME])));
exit();
