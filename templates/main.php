<?php

echo '<p><a class="underline text-red-500" href="/users/123">Users</a></p>';

use \Template\SimpleDataGrid;

use \Database\DB;

$cspReports = DB::query("SELECT * FROM `csp_reports`");

$approvedDomains = DB::query("SELECT * FROM `gtt_credentials`");

$cspPolicies = DB::query("SELECT * FROM `users`");

echo SimpleDataGrid::render($cspReports);

echo SimpleDataGrid::render($approvedDomains);

echo SimpleDataGrid::render($cspPolicies);