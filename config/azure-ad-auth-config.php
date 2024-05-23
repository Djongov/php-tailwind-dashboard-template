<?php

if (AZURE_AD_LOGIN) {

    if (!isset($_ENV['AZURE_AD_CLIENT_ID']) || !isset($_ENV['AZURE_AD_TENANT_ID'])) {
        die('AZURE_AD_CLIENT_ID, AZURE_AD_TENANT_ID, must be set in the .env file if AZURE_AD_LOGIN is set to true');
    }

    define('AZURE_AD_CLIENT_ID', $_ENV['AZURE_AD_CLIENT_ID']);
    define('AZURE_AD_TENANT_ID', $_ENV['AZURE_AD_TENANT_ID']);
    define('AZURE_AD_CLIENT_SECRET', $_ENV['AZURE_AD_CLIENT_SECRET'] ?? die('Check you if you have set the AZURE_AD_CLIENT_SECRET'));

    define('AZURE_AD_MULTITENANT', true); // Set to true if you want to allow users from any tenant to login

    if (AZURE_AD_MULTITENANT) {
        define('AZURE_AD_OAUTH_URL', 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?');
    } else {
        define('AZURE_AD_OAUTH_URL', 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/oauth2/v2.0/authorize?');
    }

    /* ID Token */

    define('AZURE_AD_ID_TOKEN_REDIRECT_URI', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/auth/azure-ad');

    $idTokenData = [
        'client_id' => AZURE_AD_CLIENT_ID,
        'response_type' => 'id_token',
        'redirect_uri' => AZURE_AD_ID_TOKEN_REDIRECT_URI,
        'response_mode' => 'form_post',
        'scope' => 'openid profile email',
        'nonce' => $_SESSION['nonce'] ?? null,
        'state' => $destination
    ];

    define('AZURE_AD_LOGIN_BUTTON_URL', AZURE_AD_OAUTH_URL . http_build_query($idTokenData));

    /* Code exchange */

    define('AZURE_AD_CODE_REDIRECT_URI', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/auth/azure/azure-ad-code-exchange');

    /* Access Token */

    //define('AZURE_AD_ACCESS_TOKEN_REDIRECT_URI', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/auth/azure/receive-access-token');

    /* Logout */
    
    define('AZURE_AD_LOGOUT_BUTTON_URL', 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/oauth2/v2.0/logout?post_logout_redirect_uri=' . $protocol . '://' . $_SERVER['HTTP_HOST']);
}
