<?php

use Logs\SystemLog;
use Response\DieCode;
use Database\MYSQL;
use Template\DataGrid;

if (!$isAdmin) {
    SystemLog::write('Got unauthorized for admin page', 'Access');
    DieCode::kill('You are not authorized to view this page', 401);
}

use Template\Html;

echo Html::h1('Administration');

$dbTables = [];

$result = MYSQL::query("SHOW TABLES");

if ($result) {
    while ($row = $result->fetch_row()) {
        $dbTables[] = $row[0];
    }
    $result->free();
}

foreach ($dbTables as $table) {
    echo DataGrid::render($table, ucfirst(str_replace("_", " ", $table)), $theme);
}
