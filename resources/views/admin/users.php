<?php

use Security\Firewall;
use Api\Output;
use DataGrid\DataGrid;
use Database\MYSQL;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

$users = MYSQL::query('SELECT * FROM users');

$usersData = $users->fetch_all(MYSQLI_ASSOC);

// Create the table
echo DataGrid::createTable('Users', $usersData, $theme);


