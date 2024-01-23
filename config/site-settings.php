<?php
// set the display errors to 1
ini_set('display_errors', 1);

/*

Branding & SEO Settings

*/

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
define("LOGO", '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
  <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
</svg>
');

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
define('SECRET_HEADER', 'secretHeader');
// Same as above
define('SECRET_HEADER_VALUE', 'badass');

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
define('AZURE_AD_LOGIN', false);
// Whether to allow users to manually register
define('MANUAL_REGISTRATION', false);

// if this env var is available, then we must be deployed into an app service and therefore control the auth settings from the app's settings as various env variable set by the platform
if (getenv('WEBSITE_AUTH_CLIENT_ID')) {
    // The only exposure of the tenant is the openid_issuer env var, but it's a url so we use regex to catch the tenant id from it
    preg_match('/https:\/\/sts.windows.net\/(.*?)\/v2.0/', getenv('WEBSITE_AUTH_OPENID_ISSUER'), $match);
    define('TENANT_ID', $match[1]);
    // The client id is in this environmental variable
    define('CLIENT_ID', getenv('WEBSITE_AUTH_CLIENT_ID'));
    // The client secret is in this environmental variable. It's not actually being used anywhere here.
    define('CLIENT_SECRET', getenv('MICROSOFT_PROVIDER_AUTHENTICATION_SECRET'));
    // Let's build the oauth URL which includes the tenant. This is where we will be sending the request to login
    define('OAUTHURL', 'https://login.microsoftonline.com/' . TENANT_ID . '/oauth2/v2.0/authorize?');
    // Let's form what the login url will be

    define('LOGIN_BUTTON_URL', '/.auth/login/aad?post_login_redirect_uri=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    // And the logout URL
    define('LOGOUT_BUTTON_URL', '/.auth/logout?post_logout_redirect_uri=/');
    // The refresh token URL, for when the token needs to be refreshed. Note that here we generate a random nonce which is supposed to be kept somewhere like in a database. This is why we currently use a static nonce
    define('REFRESH_TOKEN_URL', 'https://login.microsoftonline.com/' . $match[1] . '/oauth2/v2.0/authorize?response_type=code+id_token&redirect_uri=' . $_SERVER['HTTP_HOST'] . '/.auth/login/aad/callback&client_id=' . getenv('APPSETTING_WEBSITE_AUTH_CLIENT_ID') . '&scope=openid+profile+email&response_mode=form_post&nonce=supersecret882&state=redir=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
// We end up here if we are using our own App Registration
} else {
    // It is best if this is taken from an ENV variable instead of being hardcoded
    define('TENANT_ID', 'a31a141e-8668-4fbf-893e-beda75578778');
    // It is best if this is taken from an ENV variable instead of being hardcoded
    define('CLIENT_ID', 'c6e972e9-3c33-40ee-8ca2-1673f5c63985');
    // Set the protocol to http:// if hostname contains localhost
    $protocol = (str_contains($_SERVER['HTTP_HOST'], 'localhost')) ? 'http' : 'https';
    // This is how we form the redirect URL. Note that https:// is hardcoded, which is fine as app registrations do not allow for http:// unless it's http://localhost.
    define('REDIRECT_URI', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/auth-verify');
    // Let's build the oauth URL which includes the tenant. This is where we will be sending the request to login
    //define('OAUTHURL', 'https://login.microsoftonline.com/organizations/oauth2/v2.0/authorize?');
    define('OAUTHURL', 'https://login.microsoftonline.com/' . TENANT_ID . '/oauth2/v2.0/authorize?');

    $destination = (isset($_GET['destination'])) ? $_GET['destination'] : $_SERVER['REQUEST_URI'];
    $authenticationData = [
        'client_id' => CLIENT_ID,
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
    define('LOGIN_BUTTON_URL', $request_id_token_url);
    // For this one, the logout will be our own script
    define('LOGOUT_BUTTON_URL', 'https://login.microsoftonline.com/' . TENANT_ID . '/oauth2/v2.0/logout?post_logout_redirect_uri=https://' . $_SERVER['HTTP_HOST']);
}
