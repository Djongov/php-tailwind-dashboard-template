<?php

use Components\Alerts;

try {
    $db = new App\Database\DB(); // Initialize the DB object
    $pdo = $db->getConnection(); // Retrieve the PDO connection object
} catch (\PDOException $e) {
    $errorMessage = $e->getMessage();
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

// Now let's check if all the system tables are present
$systemTables = [
    'users',
    'cache',
    'firewall',
    'csp_reports',
    'csp_approved_domains',
    'system_log'
];

// Now let's check if these tables exist
// foreach ($systemTables as $table) {
//     $query = "SHOW TABLES LIKE $table";
//     $stmt = $pdo->prepare($query);
//     $stmt->execute();
//     $result = $stmt->fetch();
//     if (!$result) {
//         echo Alerts::danger('System table <b>' . $table . '</b> not found. Please make sure that you have ran the database migrations if you haven\'t used the /install method to deploy the app. The other options is that you probably pointed DB_NAME in the .env file to an existing database that has different tables than expected.');
//         return;
//     }
// }
