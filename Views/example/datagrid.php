<?php declare(strict_types=1);

use App\Database\DB;
use Components\Html;
use Components\DataGrid;

echo HTML::h1('DataGrid', true);

echo HTML::p('DataGrid is a special class that can be used to display data in a table format with a filterable and paginated table (datagrid) with just a few lines of code. It is a very powerful tool that can be used to display data from the database, from an API, or a PHP array. Provides a edit/delete buttons and export to csv and tsv.');

// Exampple 1: From DB Table

echo HTML::h2('Example 1: Displaying data from a MySQL table');

echo HTML::p('This example will display the data from the users table in the database. There are switches for Editing or Deleting the data. This is only available for database related DataGrids. The table is filterable and paginated.');

echo DataGrid::fromDBTable('users', 'Users', $theme);

echo HTML::horizontalLine();

// Example 2: From Query

echo HTML::h2('Example 2: Displaying data from a MySQL query');

$query = "SELECT id, theme FROM users";

echo HTML::p('This example will render data from a custom query - ' . $query . '.');

echo DataGrid::fromQuery('users', $query, 'Custom query', $theme);

echo HTML::horizontalLine();

// Example 3: From Data

echo HTML::h2('Example 3: Displaying data from a PHP array');

$users = [
    [
        'id' => 1,
        'username' => 'admin',
        'email' => 'example@example.com',
        'role' => 'admin',
        'enabled' => 1
    ],
    [
        'id' => 2,
        'username' => 'user',
        'email' => 'example2@example.com',
        'role' => 'user',
        'enabled' => 1
    ],
];

echo DataGrid::fromData('From PHP Array', $users, $theme, [
    'filters' => false,
    'ordering' => true,
    'paging' => true,
    'lengthMenu' => [[10, 50, 100], [10, 50, 100]],
]);

echo HTML::horizontalLine();

// Example 4 : Autoloading the DataGrid from Javascript, using the autoloader

echo HTML::h2('Example 4: Autoloading the DataGrid from Javascript, using the autoloader');

echo HTML::p('This example will autoload the DataGrid from Javascript, using the autoloader. The data is fetched from the database and then passed to the autoloader. Check source code to see how it is done.');

$db = new DB();

$pdo = $db->getConnection();

$usersData = $pdo->query('SELECT * FROM csp_approved_domains');

// PDO fetch now
$usersArray = $usersData->fetchAll(\PDO::FETCH_ASSOC);

$autoloadArray = [
    [
        'type' => 'table',
        'parentDiv' => 'dataGridDataLoader',
        'tableOptions' => [
            'ordering' => true,
            'paging' => true,
            'lengthMenu' => [[10, 50, 100], [10, 50, 100]],
            'filters' => true,
        ],
        'data' => $usersArray
    ]
];

foreach ($autoloadArray as $array) {
    echo '<input type="hidden" name="autoload" value="' . htmlspecialchars(json_encode($array)) . '" />';
}

echo '<div id="dataGridDataLoader" class="mx-2 my-12 flex flex-wrap flex-row justify-center items-center"></div>';

$db->__destruct();
