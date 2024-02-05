<?php

use Database\MYSQL;
use Components\Alerts;
use DataGrid\SimpleVerticalDataGrid;
use Api\Checks;
use Security\Firewall;

Firewall::activate();

$checks = new Checks($vars, $_POST);

// Perform the API checks
$checks->apiAdminChecks();

// Awaiting parameters
$allowedParams = ['query', 'csrf_token'];

// Check if the required parameters are present
$checks->checkParams($allowedParams, $_POST);

$query = $_POST['query'];

if (str_starts_with($query, 'DROP') || str_starts_with($query, 'TRUNCATE')) {
    echo Alerts::danger('You cannot execute DROP or TRUNCATE queries');
    return;
}

// JOIN not developed yet
if (str_contains($query, 'JOIN')) {
    echo Alerts::danger('You cannot execute JOIN queries as they are in development');
    return;
}

$result = MYSQL::query($query);

if (str_starts_with($query, 'SELECT')) {
    if ($result->num_rows === 0) {
        echo Alerts::danger('No data found');
        return;
    }
    $data = $result->fetch_all(MYSQLI_ASSOC);
    echo '<div class="mx-4">';
        echo SimpleVerticalDataGrid::render($data);
    echo '</div>';
} elseif (str_starts_with($query, 'DESCRIBE') || str_starts_with($query, 'SHOW')) {
    if ($result->num_rows === 0) {
        echo Alerts::danger('No data found');
        return;
    } else {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo '<div class="mx-4">';
            echo SimpleVerticalDataGrid::render($data);
        echo '</div>';
    }
} else {
    if ($result->affected_rows === 0) {
        echo Alerts::danger('No rows changed');
    } else {
        echo Alerts::success('Query executed successfully. ' . $result->affected_rows . ' rows affected');
    }
}
