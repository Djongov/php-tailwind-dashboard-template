<?php

use Google\Client;

// set the display errors to 1
ini_set('display_errors', 1);

/*

Branding & SEO Settings

*/
// Whether to show the loading screen on page load
define("SHOW_LOADING_SCREEN", true);
// Site title, Goes on footer and main menu header
define("SITE_TITLE", "Dashboard Template");
// Key words for SEO
define("GENERIC_KEYWORDS", [
    SITE_TITLE,
]);
// Site description for SEO
define("GENERIC_DESCRIPTION", "Dashboard Template");

// Logo for the SEO OG tags
define("OG_LOGO", 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/Logos/awm-full-logo-card.png');

// Used in terms of service
define("THIRD_PARTY_LIST", []);
// Used in terms of service
define("COMPANY_NAME", "Sunwell Solutions LTD");
// Used in terms of service
define("COMPANY_EMAIL", "info@sunwellsolutions.com");
// Used in terms of service
define("COMPANY_COUNTRY", "Bulgaria");
// Used in terms of service
define("COMPANY_PHONE", "+359887755355");
// Used in terms of service
define("COMPANY_ADDRESS", "Sofia, Khan Krum street 13");
// Used in terms of service
define("COMPANY_URL", "https://sunwellsolutions.com");

// Logo that sits on the menu and the footer
define("LOGO", '/assets/images/logo.jpg');

// Default set of metadata for the site, used in routes.php
define("DEFAULT_METADATA", [
    'title' => SITE_TITLE,
    'description' => GENERIC_DESCRIPTION,
    'keywords' => GENERIC_KEYWORDS,
    'thumbimage' => OG_LOGO,
]);

// Default theme for unathenticated users and first-time logins, possible values: 'amber', 'green', 'stone', 'rose', 'lime', 'teal', 'sky', 'purple', 'red', 'fuchsia', 'indigo'
define("COLOR_SCHEME", "amber");

/*

DB Settings

$_ENV is taking values from the .env file in the root of the project. If you are not using .env, hardcode them or pass them as env variables in your server

*/

define("MYSQL_SSL", filter_var($_ENV['MYSQL_SSL'], FILTER_VALIDATE_BOOLEAN));
define("DB_HOST", $_ENV['DB_HOST']);
define("DB_USER", $_ENV['DB_USER']);
define("DB_PASS", $_ENV['DB_PASS']);
define("DB_NAME", $_ENV['DB_NAME']);

// This is the DigiCertGlobalRootCA.crt.pem file that is used to verify the SSL connection to the DB. It's located in the .tools folder
define("CA_CERT", dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . '.tools' . DIRECTORY_SEPARATOR . 'DigiCertGlobalRootCA.crt.pem');
// This is used by the curl requests so you don't get SSL verification errors. It's located in the .tools folder
define("CURL_CERT", dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . '.tools' . DIRECTORY_SEPARATOR . 'cacert.pem');

// This needs to be set to what is set across the fetch requests in the javascript files. Default is the below
define('SECRET_HEADER', 'secretheader');
// Same as above
define('SECRET_HEADER_VALUE', 'badass');

/*

Mailer Settings (Sendgrid)

*/

define("SENDGRID", true);
if (SENDGRID) {
    define("SENDGRID_API_KEY", $_ENV['SENDGRID_API_KEY']);
    #define("SENDGRID_TEMPLATE_ID", 'd-381e01fdce2b44c48791d7a12683a9c3');
}

define("FROM", 'admin@gamerz-bg.com');
define("FROM_NAME", 'No Reply');

/*

Text Editor Settings (TinyMCE)

*/

define("TINYMCE", true);

if (TINYMCE) {
    define("TINYMCE_SCRIPT_LINK", 'https://cdn.tiny.cloud/1/z5zdktmh1l2u1e6mhjuer9yzde2z48kc5ctgg9wsppaobz9s/tinymce/6/tinymce.min.js');
    #define("TINYMCE_API_KEY", $_ENV['TINYMCE_API_KEY']);
}

/*

Charts

For displaying non-JS charts we utilize Quickchart.io. It's a free service that allows you to generate charts from a simple URL. We use it to generate the charts in the form of images which are suited for emailing them safely or display charts from the backend. However, we introduce QUICKCHART_HOST so you can host your own instance of Quickchart.io and use it instead of the public one. This is useful if you want to keep your data private and not send it to a third party service. If you want to host your own instance, you need an app hosting the docker image of Quickchart.io. You can find it here: ianw/quickchart:latest

*/

define("QUICKCHART_HOST", "quickchart.io");

/*

Authentication Settings

*/

// This is used by the JWT handler to sign the tokens. It's should be a base64 encoded string of the public key
define("JWT_PUBLIC_KEY", $_ENV['JWT_PUBLIC_KEY']);
// This is used by the JWT handler to sign the tokens. It's should to be a base64 encoded string of the private key
define("JWT_PRIVATE_KEY", $_ENV['JWT_PRIVATE_KEY']);
// Name of the authentication cookie which holds the JWT token
define('AUTH_COOKIE_NAME', 'auth_cookie');
// Whether to allow users to login with local accounts
define('LOCAL_USER_LOGIN', true);
// Whether to allow users to login with Azure AD accounts
define('AZURE_AD_LOGIN', true);
// Azure App registratin client id
define('AZURE_AD_CLIENT_ID', $_ENV['AZURE_AD_CLIENT_ID']);
// Azure App registration tenant id
define('AZURE_AD_TENANT_ID', $_ENV['AZURE_AD_TENANT_ID']);
// Whether to allow users to manually register
define('MANUAL_REGISTRATION', true);
// Google login
define('GOOGLE_LOGIN', true);

$destination = (isset($_GET['destination'])) ? $_GET['destination'] : $_SERVER['REQUEST_URI'];

if (GOOGLE_LOGIN) {
    define("GOOGLE_CLIENT_ID", $_ENV['GOOGLE_CLIENT_ID']);
    define("GOOGLE_CLIENT_SECRET", $_ENV['GOOGLE_CLIENT_SECRET']);
    $google_nonce = $_SESSION['nonce'] ?? null;
    $client = new Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/auth-verify');
    $client->addScope("email");
    $client->addScope("profile");
    $client->addScope("openid");
    $client->setPrompt('select_account consent');
    $client->setAccessType('offline');
    // Set the state too
    $client->setState($destination);
    // Set nonce
    $client->setLoginHint($google_nonce);
    define("GOOGLE_LOGIN_BUTTON_URL", $client->createAuthUrl());
    define("GOOGLE_LOGOUT_BUTTON_URL", 'https://accounts.google.com/logout');
    define("GOOGLE_REFRESH_TOKEN_URL", 'https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=' . GOOGLE_CLIENT_ID . '&redirect_uri=' . $_SERVER['HTTP_HOST'] . '/auth-verify&scope=openid+profile+email&state=redir=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

if (AZURE_AD_LOGIN) {
    // if this env var is available, then we must be deployed into an app service and therefore control the auth settings from the app's settings as various env variable set by the platform
    if (getenv('WEBSITE_AUTH_CLIENT_ID')) {
        // The only exposure of the tenant is the openid_issuer env var, but it's a url so we use regex to catch the tenant id from it
        preg_match('/https:\/\/sts.windows.net\/(.*?)\/v2.0/', getenv('WEBSITE_AUTH_OPENID_ISSUER'), $match);
        define('AZURE_AD_TENANT_ID', $match[1]);
        // The client id is in this environmental variable
        define('AZURE_AD_CLIENT_ID', getenv('WEBSITE_AUTH_CLIENT_ID'));
        // The client secret is in this environmental variable. It's not actually being used anywhere here.
        define('AZURE_AD_CLIENT_SECRET', getenv('MICROSOFT_PROVIDER_AUTHENTICATION_SECRET'));
        // Let's build the oauth URL which includes the tenant. This is where we will be sending the request to login
        define('OAUTHURL', 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/oauth2/v2.0/authorize?');
        // Let's form what the login url will be

        define('AZURE_AD_LOGIN_BUTTON_URL', '/.auth/login/aad?post_login_redirect_uri=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        // And the logout URL
        define('AZURE_AD_LOGOUT_BUTTON_URL', '/.auth/logout?post_logout_redirect_uri=/');
        // The refresh token URL, for when the token needs to be refreshed. Note that here we generate a random nonce which is supposed to be kept somewhere like in a database. This is why we currently use a static nonce
        define('REFRESH_TOKEN_URL', 'https://login.microsoftonline.com/' . $match[1] . '/oauth2/v2.0/authorize?response_type=code+id_token&redirect_uri=' . $_SERVER['HTTP_HOST'] . '/.auth/login/aad/callback&client_id=' . getenv('APPSETTING_WEBSITE_AUTH_CLIENT_ID') . '&scope=openid+profile+email&response_mode=form_post&nonce=' .
            $_SESSION['nonce'] ?? null  . '&state=redir=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    // We end up here if we are using our own App Registration
    } else {
        // Set the protocol to http:// if hostname contains localhost
        $protocol = (str_contains($_SERVER['HTTP_HOST'], 'localhost')) ? 'http' : 'https';
        // This is how we form the redirect URL. Note that https:// is hardcoded, which is fine as app registrations do not allow for http:// unless it's http://localhost.
        define('REDIRECT_URI', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/auth-verify');
        // Let's build the oauth URL which includes the tenant. This is where we will be sending the request to login
        //define('OAUTHURL', 'https://login.microsoftonline.com/organizations/oauth2/v2.0/authorize?');
        define('OAUTHURL', 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/oauth2/v2.0/authorize?');

        $authenticationData = [
            'client_id' => AZURE_AD_CLIENT_ID,
            'response_type' => 'id_token',
            'redirect_uri' => REDIRECT_URI,
            'response_mode' => 'form_post',
            'scope' => 'openid profile email',
            // Note that the nonce is supposed to be checked on return but you need special settings to keep it somewhere, like in a database. This is why we currently use a static nonce but i leave here a line with random nonce
            'nonce' => $_SESSION['nonce'] ?? null,
            //'nonce' => 'c0ca2663770b3c9571ca843c7106851816e2d415e77369a1',
            'state' => $destination
        ];
        // This basically merges OAUTH URL and $data
        $request_id_token_url = OAUTHURL . http_build_query($authenticationData);
        // Let's form what the login url will be
        define('AZURE_AD_LOGIN_BUTTON_URL', $request_id_token_url);
        // For this one, the logout will be our own script
        define('AZURE_AD_LOGOUT_BUTTON_URL', 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/oauth2/v2.0/logout?post_logout_redirect_uri=https://' . $_SERVER['HTTP_HOST']);
    }
}
