<?php

use Components\Html;
use Components\Alerts;

try {
    $db = new App\Database\DB(); // Initialize the DB object
    $pdo = $db->getConnection(); // Retrieve the PDO connection object
} catch (\PDOException $e) {
    $errorMessage = $e->getMessage();
    if (str_contains($errorMessage, 'Unknown database')) {
        // Pick up the database name from the error
        $databaseName = explode('Unknown database ', $errorMessage)[1];
        $errorMessage = 'Database ' . $databaseName . ' not found. Please install the application by going to ' . HTML::a('/install', '/install', $theme);
    }
    echo Alerts::danger($errorMessage); // Handle the exception
    return;
}

echo Alerts::success('Successfully connected to the database');
