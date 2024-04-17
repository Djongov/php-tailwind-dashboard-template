<?php

use App\Database\DB;
use Components\Alerts;
use Components\DataGrid\DataGrid;
use Controllers\Api\Checks;
use App\Security\Firewall;

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

$db = new DB();

$pdo = $db->getConnection();

$stmt = $pdo->prepare($query);

$stmt->execute();

$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

if (str_starts_with($query, 'SELECT')) {
    if (empty($data)) {
        echo Alerts::danger('No data found for this SELECT query');
        return;
    }
    echo '<div class="mx-4">';
        echo DataGrid::createTable('query', $data, $theme, 'Custom query results', false, false);
    echo '</div>';
} elseif (str_starts_with($query, 'DESCRIBE') || str_starts_with($query, 'SHOW')) {
    if (empty($data)) {
        echo Alerts::danger('No data found for this DESCRIBE or SHOW query');
        return;
    } else {
        echo '<div class="mx-4">';
            echo DataGrid::createTable('query', $data, $theme, 'Custom query results', false, false);
        echo '</div>';
    }
} else {
    if (empty($data)) {
        echo Alerts::danger('No rows changed');
    } else {
        echo Alerts::success('Query executed successfully. ' . $stmt->rowCount() . ' rows affected');
    }
}
