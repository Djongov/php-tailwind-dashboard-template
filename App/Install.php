<?php declare(strict_types=1);

namespace App;

use App\Database\DB;
use Components\Alerts;
use Components\Html;
use Controllers\Api\Output;
use App\Utilities\IP;
use App\Utilities\General;
use PDO;
use PDOException;

class Install
{
    public function start() : string
    {
        $html = '';
        $html .= HTML::h2('Database does not exist, attempting to create it', true);

        //Connect to the database server without specifying the database
        try {
            if (DB_DRIVER === 'mysql') {
                $dsn = sprintf("mysql:host=%s;port=%d;charset=utf8mb4", DB_HOST, DB_PORT);
                $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
                if (defined("DB_SSL") && DB_SSL) {
                    $options[PDO::MYSQL_ATTR_SSL_CA] = DB_CA_CERT;
                }
            } elseif (DB_DRIVER === 'pgsql') {
                $dsn = sprintf("pgsql:host=%s;port=%d", DB_HOST, DB_PORT);
                if (defined("DB_SSL") && DB_SSL) {
                    $dsn .= sprintf(";sslmode=require;sslrootcert=%s", DB_CA_CERT);
                }
                $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            } elseif (DB_DRIVER === 'sqlite') {
                // $dsn = 'sqlite: ' . dirname($_SERVER['DOCUMENT_ROOT']) . '/.tools/' . DB_NAME . '.db';

                // $options = [
                //     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                //     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                //     PDO::ATTR_EMULATE_PREPARES   => false,
                // ];
                $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            } else {
                throw new \Exception('Unsupported DB_DRIVER: ' . DB_DRIVER);
            }
            if (DB_DRIVER !== 'sqlite') {
                $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            }
        } catch (PDOException $e) {
            Output::error('Connection error: ' . $e->getMessage(), 400);
            return $html;
        }

        // Create the database if it doesn't exist
        try {
            if (DB_DRIVER === 'mysql') {
                $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            } elseif (DB_DRIVER === 'pgsql') {
                $conn->exec("CREATE DATABASE " . DB_NAME);
            } elseif (DB_DRIVER === 'sqlite') {
                // For sqlite we need to create the database file
                $dbFile = dirname($_SERVER['DOCUMENT_ROOT']) . '/.tools/' . DB_NAME . '.db';
                $dbDir = dirname($_SERVER['DOCUMENT_ROOT']) . '/.tools';
                $dbFile = '/' . DB_NAME . '.db';
                $dbFullPath = $dbDir . $dbFile;
                if (!is_writable($dbDir)) {
                    Output::error("Error: directory $dbDir is not writable.");
                }
                if (!file_exists($dbFullPath)) {
                    $conn = new PDO('sqlite:' . $dbFullPath);
                }
            }
        } catch (PDOException $e) {
            Output::error('Database creation error: ' . $e->getMessage(), 400);
            return $html;
        }

        // Reconnect to the newly created database
        try {
            if (DB_DRIVER === 'mysql') {
                $dsnWithDb = sprintf("mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4", DB_HOST, DB_PORT, DB_NAME);
            } elseif (DB_DRIVER === 'pgsql') {
                $dsnWithDb = sprintf("pgsql:host=%s;port=%d;dbname=%s", DB_HOST, DB_PORT, DB_NAME);
                if (defined("DB_SSL") && DB_SSL) {
                    $dsnWithDb .= sprintf(";sslmode=require;sslrootcert=%s", DB_CA_CERT);
                }
            } elseif (DB_DRIVER === 'sqlite') {
                $dsnWithDb = 'sqlite:' . dirname($_SERVER['DOCUMENT_ROOT']) . '/.tools/' . DB_NAME . '.db';
            }
            
            $conn = new PDO($dsnWithDb, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            Output::error('Database selection error: ' . $e->getMessage(), 400);
            return $html;
        }

        // Read and execute queries from the SQL file to create tables
        $migrateFile = dirname($_SERVER['DOCUMENT_ROOT']) . '/.tools/migrate.sql';
        $migrate = file_get_contents($migrateFile);

        try {
            // Execute multiple queries
            $conn->exec($migrate);
        } catch (PDOException $e) {
            Output::error('Error in migrate file: ' . $e->getMessage(), 400);
            return $html;
        }

        $ip = IP::currentIP();

        // Now you can execute additional queries
        try {
            $stmt = $conn->prepare("INSERT INTO csp_approved_domains (domain, created_by) VALUES (?, 'System')");
            $stmt->execute([$_SERVER['HTTP_HOST']]);
        } catch (PDOException $e) {
            Output::error('Inserting csp_approved_domains rule for host ' . $_SERVER['HTTP_HOST'] . ' error: ' . $e->getMessage(), 400);
            return $html;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO firewall (ip_cidr, created_by, comment) VALUES (?, 'System', 'Initial Admin IP')");
            $stmt->execute([$ip . '/32']);
        } catch (PDOException $e) {
            Output::error('Inserting firewall rule for IP ' . $ip . ' error: ' . $e->getMessage(), 400);
            return $html;
        }

        // Insert administrator for first time login but only if local login is used
        if (LOCAL_USER_LOGIN) {
            $password = General::randomString(12);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $countryCode = 'US';
            if (IP::isPublicIp($ip)) {
                $ipGeoLocate = \App\Request\NativeHttp::get('http://ip-api.com/json/' . $ip, [], true);
                if ($ipGeoLocate['status'] === 'success') {
                    $countryCode = $ipGeoLocate['countryCode'];
                }
            }

            try {
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, name, last_ips, origin_country, role, last_login, theme, provider, enabled) VALUES ('admin', ?, 'admin', 'admin', ?, ?, 'administrator', ?, ?, 'local', CAST(? AS BOOLEAN));");
                $now = (DB_DRIVER === 'sqlite') ? 'CURRENT_TIMESTAMP' : 'NOW()';
                $stmt->execute([$hashedPassword, $ip, $countryCode, $now, COLOR_SCHEME, 1]);
            } catch (PDOException $e) {
                Output::error('Inserting admin user error: ' . $e->getMessage(), 400);
                return $html;
            }
            // Print the credentials to the screen
            $html .= Alerts::success('Database "' . DB_NAME . '" and system tables created successfully. Please go to <a class="underline" href="/login">Login</a> page. Use "admin" as username. Do not refresh the page until you have copied the password below.');
            $html .= HTML::p('<span class="c0py">' . $password . '</span>');
        } else {
            $html .= Alerts::success('Database "' . DB_NAME . '" and system tables created successfully. Please go to <a class="underline" href="/login">Login</a> page. Because no local login is enabled you need to control admin accounts through the provider claims.');
        }

        return $html;
    }
}
