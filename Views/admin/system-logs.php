<?php

use App\Security\Firewall;
use Components\DataGrid;
use Controllers\Api\Output;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

echo DataGrid::fromDBTable('system_log', 'System Logs', $theme);
