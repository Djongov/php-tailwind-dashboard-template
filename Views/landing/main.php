<?php

use Components\Html;
use Components\Alerts;

/*
use Request\NativeHttp;
$request = NativeHttp::get('https://www.ipqualityscore.com/api/json/leaked/email/IgJi2FQr2iU11QkbkBtoWun5f1YmSk8y/djongov@gamerz-bg.com', [], true);

$array = [
    'to' => [
        [
            'email' => 'djongov@gamerz-bg.com',
            'name' => 'Djo'
        ]
    ],
    'subject' => 'Test',
    'body' => 'Test body'
];

$headers = [
    'x-api-key: 57a7c24eb9a17c9b8e0d149586143c0e9c518083185375b8ff994a67b99c4df0'
];

*/


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

