<?php
define("SITE_TITLE", "UEFA DevOps Portal");
define("GENERIC_KEYWORDS", [
    SITE_TITLE,
]);

define("COLOR_SCHEME", "sky");

define("GENERIC_DESCRIPTION", "Portal of the UEFA DevOps Team");

define("LOGO", '/');

if ($_SERVER['HTTP_HOST'] === 'dashboard-template') {
    define("DB_MODE", 'mysql');
    define("DB_USER", 'root');
    define("DB_HOST", 'localhost');
    define("DB_PASSWORD", '19MySQL86$');
    define("DB_NAME", 'security-dashboard');
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
    define("DB_NAME", 'uefa-devops-portal');
    
}

define("CA_CERT", $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'DigiCertGlobalRootCA.crt.pem');

define("Login_Button_URL", '/');