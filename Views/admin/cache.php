<?php declare(strict_types=1);

use App\Security\Firewall;
use Controllers\Api\Output;
use Components\DataGrid;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

echo DataGrid::fromDBTable('cache', 'Cache', $theme);
