<?php
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

// Logo that sits on the menu and the footer
define("LOGO", '/assets/images/logo.jpg');

// Logo for the SEO OG tags
define("OG_LOGO", 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/logo.jpg');

/* Color Scheme */
// Default theme for unathenticated users and first-time logins, possible values: 'amber', 'green', 'stone', 'rose', 'lime', 'teal', 'sky', 'purple', 'red', 'fuchsia', 'indigo'

// This is a default color scheme for small parts such as buttons and links
define("COLOR_SCHEME", "amber");

// This is the text while in the light mode
define("TEXT_COLOR_SCHEME", "text-gray-900");
// This is the text while in the dark mode
define("TEXT_DARK_COLOR_SCHEME", "dark:text-gray-100");
// This is the background color while in the light mode
define("LIGHT_COLOR_SCHEME_CLASS", "bg-purple-300");
// This is the background color while in the dark mode
define("DARK_COLOR_SCHEME_CLASS", "dark:bg-purple-900");
// This is the background color for the body while in the light mode
define("BODY_COLOR_SCHEME_CLASS", "bg-purple-200");
// This is the background color for the body while in the dark mode
define("BODY_DARK_COLOR_SCHEME_CLASS", "dark:bg-purple-800");

// Data grid color schemes

// This is the background color for the table body while in the light mode
define("DATAGRID_TBODY_COLOR_SCHEME", "bg-gray-100");
// This is the background color for the table body while in the dark mode
define("DATAGRID_TBODY_DARK_COLOR_SCHEME", "dark:bg-gray-800");
// This is the background color for the table head while in the light mode
define("DATAGRID_THEAD_COLOR_SCHEME", "bg-gray-300");
// This is the background color for the table head while in the dark mode
define("DATAGRID_THEAD_DARK_COLOR_SCHEME", "dark:bg-gray-700");
// This is the table text color while in the light mode
define("DATAGRID_TEXT_COLOR_SCHEME", "text-gray-900");
// This is the table text color while in the dark mode
define("DATAGRID_TEXT_DARK_COLOR_SCHEME", "dark:text-gray-100");

// Do a check here if .env file exists
if (!file_exists(dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . '.env')) {
    die('The .env file is missing. Please create one in the root of the project or use the <a href="/create-env">helper</a>');
}

// Load the environment variables from the .env file which resides in the root of the project
$dotenv = \Dotenv\Dotenv::createImmutable(dirname($_SERVER['DOCUMENT_ROOT']));

try {
    $dotenv->load();
} catch (\Exception $e) {
    die($e->getMessage());
}

/*

DB Settings

$_ENV is taking values from the .env file in the root of the project. If you are not using .env, hardcode them or pass them as env variables in your server

*/
$requiredDBConstants = [
    'DB_SSL',
    'DB_DRIVER',
    'DB_HOST',
    'DB_USER',
    'DB_PASS',
    'DB_NAME',
    'LocalLoginEnabled',
    'GoogleLoginEnabled',
    'MicrosoftLiveLoginEnabled',
    'EntraIDLoginEnabled',
    'SENDGRID_ENABLED'
];

foreach ($requiredDBConstants as $constant) {
    if (!isset($_ENV[$constant])) {
        die($constant . ' must be set in the .env file');
    }
}

define("DB_SSL", filter_var($_ENV['DB_SSL'], FILTER_VALIDATE_BOOLEAN));
define("DB_DRIVER", $_ENV['DB_DRIVER']);
define("DB_HOST", $_ENV['DB_HOST']);
define("DB_USER", $_ENV['DB_USER']);
define("DB_PASS", $_ENV['DB_PASS']);
define("DB_NAME", $_ENV['DB_NAME']);
define("DB_PORT", $_ENV['DB_PORT']);

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

define("SENDGRID", filter_var($_ENV['SENDGRID_ENABLED'], FILTER_VALIDATE_BOOLEAN));

if (SENDGRID) {
    if (!isset($_ENV['SENDGRID_API_KEY'])) {
        die('SENDGRID_API_KEY must be set in the .env file');
    }
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
    define("TINYMCE_SCRIPT_LINK", 'https://cdn.tiny.cloud/1/z5zdktmh1l2u1e6mhjuer9yzde2z48kc5ctgg9wsppaobz9s/tinymce/7/tinymce.min.js');
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

// Name of the authentication cookie which holds the JWT token
define('AUTH_HANDLER', 'cookie'); // cookie/session
define('JWT_TOKEN_EXPIRY', 3600);
define('AUTH_COOKIE_EXPIRY', 86400); // In case cookie is used for handler, make the duration 1 day. Even if Azure tokens cannot exceed 1 hour, if cookie is present it will redirect on its own to refresh the token, so for best user experience it's good to have a longer duration than the token itself

if (AUTH_HANDLER === 'cookie') {
    define('AUTH_COOKIE_NAME', 'auth_cookie');
} elseif (AUTH_HANDLER === 'session') {
    define('AUTH_SESSION_NAME', 'auth_session');
} else {
    die('AUTH_HANDLER must be set to cookie or session');
}

$destination = (isset($_GET['destination'])) ? $_GET['destination'] : $_SERVER['REQUEST_URI'];
$protocol = (str_contains($_SERVER['HTTP_HOST'], 'localhost')) ? 'http' : 'https';

// Whether to allow users to login with local accounts
define('LOCAL_USER_LOGIN', filter_var($_ENV['LocalLoginEnabled'], FILTER_VALIDATE_BOOLEAN));
if (LOCAL_USER_LOGIN) {
    if (!isset($_ENV['JWT_PUBLIC_KEY']) || !isset($_ENV['JWT_PRIVATE_KEY'])) {
        die('JWT_PUBLIC_KEY and JWT_PRIVATE_KEY must be set in the .env file');
    }
    // This is used by the JWT handler to sign the tokens. It's should be a base64 encoded string of the public key
    define("JWT_PUBLIC_KEY", $_ENV['JWT_PUBLIC_KEY']);
    // This is used by the JWT handler to sign the tokens. It's should to be a base64 encoded string of the private key
    define("JWT_PRIVATE_KEY", $_ENV['JWT_PRIVATE_KEY']);
    // Whether to allow users to manually register
    define('MANUAL_REGISTRATION', true);
}
// Whether to allow users to login with Azure AD accounts
define('AZURE_AD_LOGIN', filter_var($_ENV['EntraIDLoginEnabled'], FILTER_VALIDATE_BOOLEAN));
if (AZURE_AD_LOGIN) {
    include_once dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'azure-ad-auth-config.php';
}
define('MICROSOFT_LIVE_LOGIN', filter_var($_ENV['MicrosoftLiveLoginEnabled'], FILTER_VALIDATE_BOOLEAN));
if (MICROSOFT_LIVE_LOGIN) {
    include_once dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'microsoft-live-auth-config.php';
}
// Google login
define('GOOGLE_LOGIN', filter_var($_ENV['GoogleLoginEnabled'], FILTER_VALIDATE_BOOLEAN));
if (GOOGLE_LOGIN) {
    include_once dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'google-auth-config.php';
}

/* App checks */
$missing_extensions = [];

$required_extensions = [
    'curl',
    'openssl',
    'intl'
];

if (DB_DRIVER === 'pgsql') {
    $required_extensions[] = 'pdo_pgsql';
}

if (DB_DRIVER === 'sqlsrv') {
    $required_extensions[] = 'pdo_sqlsrv';
}

if (DB_DRIVER === 'sqlite') {
    $required_extensions[] = 'pdo_sqlite';
}

if (DB_DRIVER === 'mysql') {
    $required_extensions[] = 'pdo_mysql';
}

foreach ($required_extensions as $extension) {
    if (!extension_loaded($extension)) {
        $missing_extensions[] = $extension;
    }
}

if (!empty($missing_extensions)) {
    die('The following extensions are missing: ' . implode(', ', $missing_extensions));
}
