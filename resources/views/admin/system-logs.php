<?php

use Security\Firewall;
use Api\Output;
use DataGrid\DataGridDBTable;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

echo DataGridDBTable::renderTable('system_log', $theme);
