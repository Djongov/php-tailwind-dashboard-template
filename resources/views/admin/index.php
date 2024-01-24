<?php

use Template\Html;
use Database\MYSQL;
use DataGrid\SimpleVerticalDataGrid;
use Security\Firewall;
use Api\Output;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

//var_dump($usernameArray);

$dbTables = [];

$result = MYSQL::query("SHOW TABLES");

if ($result) {
    while ($row = $result->fetch_row()) {
        $dbTables[] = $row[0];
    }
    $result->free();
}
echo '<div class="p-4 m-4 max-w-md bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
    echo HTML::h2('Database Tables', true);
    echo HTML::p('Connected to DB: ' . DB_NAME);
    echo HTML::p('DB Host: ' . DB_HOST);
    echo HTML::p('DB User: ' . DB_USER);
    echo HTML::p('Using SSL: ' . (MYSQL_SSL ? 'Yes' : 'No'));
    echo HTML::p('Total tables: ' . count($dbTables));
    echo SimpleVerticalDataGrid::render($dbTables);
echo '</div>';


