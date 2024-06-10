<?php

use Controllers\Api\Output;
use App\Install;
use Components\Alerts;
try {
    if (DB_DRIVER === 'mysql') {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        if (defined("DB_SSL") && DB_SSL) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = CA_CERT;
        }
    } elseif (DB_DRIVER === 'pgsql') {
        $dsn = 'pgsql:host=' . DB_HOST . ';dbname=' . DB_NAME;
        
        if (defined("DB_SSL") && DB_SSL) {
            $dsn .= ';sslmode=require;sslrootcert=' . CA_CERT;
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    } else {
        throw new Exception('Unsupported DB_DRIVER: ' . DB_DRIVER);
    }

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo Alerts::info('Successfully connected to the database. Nothing to do here.');
} catch (PDOException $e) {
    $error = $e->getMessage();
    if (DB_DRIVER === 'mysql' && strpos($error, "Unknown database") !== false) {
        $dsn_without_db = 'mysql:host=' . DB_HOST . ';charset=utf8';
    } elseif (DB_DRIVER === 'pgsql' && strpos($error, "does not exist") !== false) {
        $dsn_without_db = 'pgsql:host=' . DB_HOST;
        if (defined("DB_SSL") && DB_SSL) {
            $dsn_without_db .= ';sslmode=require;sslrootcert=' . CA_CERT;
        }
    } else {
        Output::error($error, 400);
        exit;
    }

    try {
        $pdo = new PDO($dsn_without_db, DB_USER, DB_PASS, $options);
        $install = new Install();
        echo $install->start($pdo);
    } catch (PDOException $e) {
        Output::error($e->getMessage(), 400);
    }
}
