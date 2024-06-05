<?php

use Controllers\Api\Output;
use App\Authentication\JWT;

if (isset($_POST['error'], $_POST['error_description'])) {
    if (str_contains($_POST['error'], 'consent_required')) {
        // Send an Authorization request if the error is AADSTS65001 (consent_required)
        $data = [
            'client_id' => AZURE_AD_CLIENT_ID,
            'response_type' => 'code',
            'redirect_uri' => $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] , // redirect back to the same page
            'scope' => 'https://graph.microsoft.com/user.read',
            'response_mode' => 'form_post',
            'state' => $_POST['state'],
            'nonce' => $_SESSION['nonce'],
            'prompt' => 'consent',
            'login_hint' => $username
        ];

        header('Location: ' . AZURE_AD_OAUTH_URL . http_build_query($data));
        exit();
    }
    if (str_contains($_POST['error'], 'login_required')) {
        // Send an Authorization request if the error is AADSTS50058 (login_required)
        // $data = [
        //     'client_id' => AZURE_AD_CLIENT_ID,
        //     'response_type' => 'code',
        //     'redirect_uri' => $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] , // redirect back to the same page
        //     'scope' => 'https://graph.microsoft.com/user.read',
        //     'response_mode' => 'form_post',
        //     'state' => $_POST['state'],
        //     'nonce' => $_SESSION['nonce'],
        //     'prompt' => 'login',
        //     'login_hint' => $username
        // ];

        // header('Location: ' . AZURE_AD_OAUTH_URL . http_build_query($data));
        // exit();
        Output::error("App Registration Error: " . $_POST['error'] . " with Description: " . $_POST['error_description']);
    }

    Output::error("Azure Error: " . $_POST['error'] . " with Description: " . $_POST['error_description']);
}

if (isset($_POST['code'], $_POST['state'], $_POST['session_state'])) {
    $code = $_POST['code'];

    $tokenUrl = AZURE_AD_TOKEN_URL;
    $postData = [
        'grant_type' => 'authorization_code',
        'client_id' => AZURE_AD_CLIENT_ID,
        'client_secret' => AZURE_AD_CLIENT_SECRET,
        'code' => $code,
        'redirect_uri' => AZURE_AD_CODE_REDIRECT_URI
    ];

    $client = new App\Request\HttpClient($tokenUrl);

    $request = $client->call('POST', '', $postData, null, false, [], true);

    if (isset($request['response'])) {
        $response = json_decode($request['response'], true);
    } elseif (isset($request['access_token'])) {
        // Find out the username in the token
        $username = JWT::parseTokenPayLoad($request['access_token'])['upn'];
        App\Authentication\AccessTokenCache::save($request['access_token'], $username);
        // Remove the username query string from state
        if (isset($_POST['state'])) {
            $split = explode("&", $_POST['state']);
            $state = $_POST['state'] ?? '/';
            $state = $split[0];
        } else {
            $state = '/';
        }
        header('Location: ' . $state);
        exit();
    } else {
        Output::error('Error: ' . $request['error'], 400);
    }

    if (isset($response['error_description'])) {
        // AADSTS70008: The provided authorization code or refresh token has expired due to inactivity. Send a new interactive authorization request for this user and resource
        if (str_contains($response['error_description'], 'AADSTS70008') || str_contains($response['error_description'], 'AADSTS54005')) {

            $data = [
                'client_id' => AZURE_AD_CLIENT_ID,
                'response_type' => 'code',
                'redirect_uri' => $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] , // redirect back to the same page
                'scope' => 'https://graph.microsoft.com/user.read',
                'response_mode' => 'form_post',
                'state' => $_POST['state'],
                'nonce' => $_SESSION['nonce'],
                'prompt' => 'consent',
                'login_hint' => $username
            ];

            header('Location: ' . AZURE_AD_OAUTH_URL . http_build_query($data));
            exit();
        } else {
            Output::error($response['error_description'], 400);
        }
    }
}

// Azure AD access token here
if (isset($_POST['access_token'], $_POST['token_type'], $_POST['expires_in'], $_POST['scope'], $_POST['state'], $_POST['session_state'])) {
    // Find out the username in the token
    $username = JWT::parseTokenPayLoad($_POST['access_token'])['upn'];
    App\Authentication\AccessTokenCache::save($_POST['access_token'], $username);
    // Remove the username query string from state
    $split = explode("&", $_POST['state']);
    $state = $_POST['state'] ?? '/';
    $state = $split[0];
    header('Location: ' . $state);
    exit();
}
