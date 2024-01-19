<?php

use Template\DataGrid;
use Security\Firewall;
use Api\Output;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

//var_dump($usernameArray);

echo DataGrid::render('cache', 'Cache', $theme);
