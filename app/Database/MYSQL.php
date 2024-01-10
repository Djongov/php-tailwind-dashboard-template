<?php

namespace Database;

if (!defined('DB_HOST')) {
    define('SITE_SETTINGS_INCLUDED', true);
    include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/site-settings.php';
}

use \Response\DieCode;
use Exception;

class MYSQL
{
    public static function connect()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn = mysqli_init();

        if (defined("MYSQL_SSL") && MYSQL_SSL) {
            mysqli_ssl_set($conn, NULL, NULL, CA_CERT, NULL, NULL);
            try {
                $conn->real_connect('p:' . DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306, MYSQLI_CLIENT_SSL);
            } catch (\mysqli_sql_exception $e) {
                $error = $e->getMessage();
                // Let's check if the Database exists
                if (str_contains($error, "Unknown database") !== false) {
                    // If it doesn't, let's create it
                    $conn->real_connect('p:' . DB_HOST, DB_USER, DB_PASS, '', 3306, MYSQLI_CLIENT_SSL);
                    $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
                    $conn->real_connect('p:' . DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306, MYSQLI_CLIENT_SSL);
                    // selct the db
                    $conn->select_db(DB_NAME);
                    // create the tables from the migrate.sql file
                    $migrateFile =  dirname($_SERVER['DOCUMENT_ROOT']) . '/migrate.sql';
                    $migrate = file_get_contents($migrateFile);
                    $conn->multi_query($migrate);
                    // Now add the current domain to the csp_approved_domains table
                    $conn->query("INSERT INTO `csp_approved_domains` (`domain`) VALUES ('" . $_SERVER['HTTP_HOST'] . "')");
                    // close the connection
                    $conn->close();
                    echo "Database " . DB_NAME . " created successfully and tables created. Please refresh the page.";
                    header("Refresh:0");
                    exit();
                } else {
                    $error = $e->getMessage();
                    Output::error($error, 400);
                }
            }
        } else {
            try {
                $conn->real_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            } catch (\mysqli_sql_exception $e) {
                $error = $e->getMessage();
                // Let's check if the Database exists
                if (str_contains($error, "Unknown database") !== false) {
                    // Connect to MySQL without specifying the database
                    $conn->real_connect(DB_HOST, DB_USER, DB_PASS);

                    // Create the database if it doesn't exist
                    $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

                    // Connect to the newly created or existing database
                    $conn->real_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

                    // Verify that the connection is successful
                    if ($conn->connect_error) {
                        throw new Exception("Failed to connect to the database: " . $conn->connect_error);
                    }

                    // Select the database
                    $conn->select_db(DB_NAME);

                    // Read and execute queries from the SQL file to create tables
                    $migrateFile = dirname($_SERVER['DOCUMENT_ROOT']) . '/.tools/migrate.sql';
                    $migrate = file_get_contents($migrateFile);

                    if ($conn->multi_query($migrate)) {
                        echo "Database " . DB_NAME . " created successfully and tables created. Please refresh the page.";
                        header("Refresh:0");
                    } else {
                        throw new Exception("Error creating tables: " . $conn->error);
                    }
                    // close the connection
                    $conn->close();
                    echo "Database " . DB_NAME . " created successfully and tables created. Please refresh the page.";
                    header("Refresh:0");
                    exit();
                } else {
                    $error = $e->getMessage();
                    Output::error($error, 400);
                }
            }
        }

        return $conn;
    }

    public static function query($query)
    {
        $link = self::connect();
        try {
            $stmt = $link->prepare($query);
        } catch (\mysqli_sql_exception $e) {
            $error = $e->getMessage();
            Output::error($error, 400);
        }
        $result = null;
        try {
            $stmt->execute();
            $result = $stmt->get_result();
        } catch (\mysqli_sql_exception $e) {
            $error = $e->getMessage();
            Output::error($error, 400);
        }

        $link->close();
        return $result;
    }

    // Prepared query, statement needs to be passed as array [] as in [$value1, $value2], returns a result
    public static function queryPrepared($query, $statement)
    {
        $link = self::connect();
        try {
            $stmt = $link->prepare($query);
        } catch (Exception $e) {
            Output::error($e, 400);
        }
        if (is_array($statement)) {
            $statementParams = '';
            foreach ($statement as $param) {
                if (is_numeric($param)) {
                    $statementParams .= 'i';
                } else {
                    $statementParams .= 's';
                }
            }
            $stmt->bind_param($statementParams, ...$statement);
        } else {
            $stmt->bind_param("s", $statement);
        }
        try {
            if ($stmt->execute()) {
                if (stripos($query, "SELECT") !== false) {
                    $result = $stmt->get_result();
                    $link->close();
                    return $result;
                } else {
                    $link->close();
                    return $stmt;
                }
            } else {
                $error = $stmt->error;
                $link->close();
                Output::error($error, 400);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            $link->close();
            Output::error($error, 400);
        }
    }
    // Sometimes you may need to make a bundle of queries one after the other, returns a result
    public static function multiQuery($arrayOfQueries)
    {
        $link = self::connect();
        foreach ($arrayOfQueries as $sql) {
            try {
                $result = mysqli_query($link, $sql);
            } catch (Exception $e) {
                $error = $e->getMessage();
                Output::error($error, 400);
            }
        }
        $link->close();
        return $result;
    }
    // This would apply mysqli_real_escape_string to an entire assoc array
    public static function mysqliEscapeAssoc($array)
    {
        $link = self::connect();
        foreach ($array as $column => $value) {
            $array[$column] = mysqli_real_escape_string($link, $array[$column]);
        }
        $link->close();
        return $array;
    }
    // This would apply mysqli_real_escape_string to a string
    public static function mysqliEscapeString($string)
    {
        $link = self::connect();
        // mysqli_real_escape_string does not accept nulls
        if ($string !== null) {
            return mysqli_real_escape_string($link, $string);
        } else {
            return $string;
        }
    }
    // Helper function for quickly save the last login time for a user
    public static function recordLastLogin($username)
    {
        //$link = self::connect();
        //date("yyyy-MM-dd HH:mm",time())
        self::queryPrepared("UPDATE `users` SET `last_login`= NOW() WHERE `username` = ?", [$username]);
    }
}
