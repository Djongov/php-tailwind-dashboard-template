<?php

use Components\Html;
use App\Database\DB;
use App\Security\Firewall;
use Controllers\Api\Output;
use App\General;
use Components\DataGrid\DataGrid;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

//var_dump($usernameArray);

$dbTables = [];

$db = new DB();

$pdo = $db->getConnection();

$stmt = $pdo->prepare("SHOW TABLES");

$stmt->execute();

// Get the PDO result

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
        $dbTables[] = $row[0];
    }
}

echo '<div class="p-4 m-4 max-w-md bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
    echo HTML::h2('Database Tables', true);
    echo HTML::p('Connected to DB: ' . DB_NAME);
    echo HTML::p('DB Host: ' . DB_HOST);
    echo HTML::p('DB User: ' . DB_USER);
    echo HTML::p('Using SSL: ' . (DB_SSL ? 'Yes' : 'No'));
    echo HTML::p('Total tables: ' . count($dbTables));
    echo DataGrid::createTable('Tables', General::assocToIndexed($dbTables), $theme, 'Tables', false, false);
echo '</div>';


