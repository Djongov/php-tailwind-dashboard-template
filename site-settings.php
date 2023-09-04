<?php
define("SITE_TITLE", "UEFA DevOps Portal");
define("GENERIC_KEYWORDS", [
    SITE_TITLE,
]);

define("COLOR_SCHEME", "sky");

define("GENERIC_DESCRIPTION", "Portal of the UEFA DevOps Team");

define("LOGO", '/assets/images/logo.jpg');

/* DB Settings */

define ("MYSQL_SSL", false);

if ($_SERVER['HTTP_HOST'] === 'dashboard-template') {
    define("DB_MODE", 'mysql');
    define("DB_USER", 'root');
    define("DB_HOST", 'localhost');
    define("DB_PASSWORD", '19MySQL86$');
    define("DB_NAME", 'uefa-devops-portal');
} elseif ($_SERVER['HTTP_HOST'] === 'uefa-devops-portal.azurewebsites.net') {
    $value = getenv('MYSQLCONNSTR_localdb');
    $connectstr_dbhost = preg_replace("/^.*Data Source=(.+?);.*$/", "\\1", $value);
    $connectstr_dbname = preg_replace("/^.*Database=(.+?);.*$/", "\\1", $value);
    $connectstr_dbusername = preg_replace("/^.*User Id=(.+?);.*$/", "\\1", $value);
    $connectstr_dbpassword = preg_replace("/^.*Password=(.+?)$/", "\\1", $value);

    define('DB_NAME', $connectstr_dbname);
    /** MySQL database username */
    define('DB_USER', $connectstr_dbusername);
    /** MySQL database password */
    define('DB_PASSWORD', $connectstr_dbpassword);
    /** MySQL hostname : this contains the port number in this format host:port . Port is not 3306 when using this feature*/
    define('DB_HOST', $connectstr_dbhost);
    define("DB_NAME", 'local_db');
}

define("CA_CERT", $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'DigiCertGlobalRootCA.crt.pem');

define("CURL_CERT", $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cacert.pem');

/* Menu Settings */

define("MAIN_MENU", [
    'Users' => [
        'icon' => [
            'type' => 'svg',
            'src' => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <rect x="10" y="9" width="4" height="12" rx="1.105" />
                <rect x="17" y="3" width="4" height="18" rx="1.105" />
                <circle cx="5" cy="19" r="2" />',
        ],
        'link' => '/users',
    ],
    '404' => [
        'link' => '/404'
    ]
]);

/* Username drop down menu */

define("USERNAME_DROPDOWN_MENU", [
    'User Settings' => [
        'path' => '/user-settings',
        'admin' => false
    ],
    'Admin' => [
        'path' => '/adminx',
        'admin' => true,
    ],
    'Logout' => [
        'path' => '/logout',
        'admin' => false,
    ]
]);

// Authentication Settings

// if this env var is available, then we must be deployed into an app service and therefore control the auth settings from the app's settings as various env variable set by the platform
if (getenv('WEBSITE_AUTH_CLIENT_ID')) {
    // The only exposure of the tenant is the openid_issuer env var, but it's a url so we use regex to catch the tenant id from it
    preg_match('/https:\/\/sts.windows.net\/(.*?)\/v2.0/', getenv('WEBSITE_AUTH_OPENID_ISSUER'), $match);
    define('Tenant_ID', $match[1]);
    // The client id is in this environmental variable
    define('Client_ID', getenv('WEBSITE_AUTH_CLIENT_ID'));
    // The client secret is in this environmental variable. It's not actually being used anywhere here.
    define('Client_Secret', getenv('MICROSOFT_PROVIDER_AUTHENTICATION_SECRET'));
    // Let's build the oauth URL which includes the tenant. This is where we will be sending the request to login
    define('OAUTHURL', 'https://login.microsoftonline.com/' . Tenant_ID . '/oauth2/v2.0/authorize?');
    // Let's form what the login url will be

    define('Login_Button_URL', '/.auth/login/aad?post_login_redirect_uri=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    // And the logout URL
    define('Logout_Button_URL', '/.auth/logout?post_logout_redirect_uri=/');
    // The refresh token URL, for when the token needs to be refreshed. Note that here we generate a random nonce which is supposed to be kept somewhere like in a database. This is why we currently use a static nonce
    define('Refresh_token_URL', 'https://login.microsoftonline.com/' . $match[1] . '/oauth2/v2.0/authorize?response_type=code+id_token&redirect_uri=' . $_SERVER['HTTP_HOST'] . '/.auth/login/aad/callback&client_id=' . getenv('APPSETTING_WEBSITE_AUTH_CLIENT_ID') . '&scope=openid+profile+email&response_mode=form_post&nonce=supersecret882&state=redir=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    // We end up here if we are using our own App Registration
} else {
    // It is best if this is taken from an ENV variable instead of being hardcoded
    define('Tenant_ID', 'a31a141e-8668-4fbf-893e-beda75578778');
    // It is best if this is taken from an ENV variable instead of being hardcoded
    define('Client_ID', 'c6e972e9-3c33-40ee-8ca2-1673f5c63985');
    // Set the protocol to http:// if hostname contains localhost
    $protocol = (str_contains($_SERVER['HTTP_HOST'], 'localhost')) ? 'http' : 'https';
    // This is how we form the redirect URL. Note that https:// is hardcoded, which is fine as app registrations do not allow for http:// unless it's http://localhost.
    define('Redirect_URI', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/auth-verify');
    // Let's build the oauth URL which includes the tenant. This is where we will be sending the request to login
    //define('OAUTHURL', 'https://login.microsoftonline.com/organizations/oauth2/v2.0/authorize?');
    define('OAUTHURL', 'https://login.microsoftonline.com/' . Tenant_ID . '/oauth2/v2.0/authorize?');

    $destination = (isset($_GET['destination'])) ? $_GET['destination'] : $_SERVER['REQUEST_URI'];
    $data = [
        'client_id' => Client_ID,
        'response_type' => 'id_token',
        'redirect_uri' => Redirect_URI,
        'response_mode' => 'form_post',
        'scope' => 'openid profile email',
        // Note that the nonce is supposed to be checked on return but you need special settings to keep it somewhere, like in a database. This is why we currently use a static nonce but i leave here a line with random nonce
        // 'nonce' => bin2hex(random_bytes(24))
        'nonce' => 'c0ca2663770b3c9571ca843c7106851816e2d415e77369a1',
        'state' => $destination
    ];
    // This basically merges OAUTH URL and $data
    $request_id_token_url = OAUTHURL . http_build_query($data);
    // Let's form what the login url will be
    define('Login_Button_URL', $request_id_token_url);
    // For this one, the logout will be our own script
    define('Logout_Button_URL', '/logout');
}

define('AUTH_COOKIE_NAME', 'auth_cookie');

define('LOCAL_USER_LOGIN', true);

define('MANUAL_REGISTRATION', true);
