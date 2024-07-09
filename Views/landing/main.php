<?php declare(strict_types=1);

use Components\Alerts;

try {
    $db = new App\Database\DB(); // Initialize the DB object
    $pdo = $db->getConnection(); // Retrieve the PDO connection object
} catch (\PDOException $e) {
    $errorMessage = $e->getMessage();
    error_log("Caught PDOException: " . $errorMessage);

    // MySQL error code 1049 is for unknown database
    if (str_contains($errorMessage, 'Unknown database')) {
        // Pick up the database name from the error
        $databaseName = explode('Unknown database ', $errorMessage)[1];
        $errorMessage = 'Database ' . $databaseName . ' not found. Please install the application by going to ' . Components\Html::a('/install', '/install', $theme);
    }
    // Postgres 08006 is for connection failure database does not exist
    if (str_contains($errorMessage, 'does not exist')) {
        $databaseName = explode('database ', $errorMessage)[1];
        $errorMessage = 'Database ' . $databaseName . '. Please install the application by going to ' . Components\Html::a('/install', '/install', $theme);
    }
    echo Alerts::danger($errorMessage); // Handle the exception
    return;
}


echo Alerts::success('Successfully connected to the database');
