<?php

use App\General;
use Database\MYSQL;



$usersData = MYSQL::query('SELECT * FROM `csp_approved_domains`');

$usersArray = $usersData->fetch_all(MYSQLI_ASSOC);

$autoloadArray = [
    [
        'type' => 'table',
        'parentDiv' => 'dataGridDataLoader',
        //'data' => General::assocToIndexed($_SERVER)
        'data' => $usersArray
    ]
];

foreach ($autoloadArray as $array) {
    echo '<input type="hidden" name="autoload" value="' . htmlspecialchars(json_encode($array)) . '" />';
}

echo '<div id="dataGridDataLoader" class="mx-2 my-12 flex flex-wrap flex-row justify-center items-center"></div>';
