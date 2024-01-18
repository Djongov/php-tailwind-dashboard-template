<?php

use Logs\SystemLog;
use Api\Output;
use Database\MYSQL;
use DataGrid\SimpleVerticalDataGrid;

if (!$isAdmin) {
    SystemLog::write('Got unauthorized for admin page', 'Access');
    Output::error('You are not authorized to view this page', 401);
}

use Template\Html;

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
    echo SimpleVerticalDataGrid::render($dbTables);
echo '</div>';


