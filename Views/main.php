<?php

use \Template\SimpleDataGrid;

use \Database\DB;

/*
$url = 'http://ip-api.com/json/';

use \Request\Http;

$newRequest = new Http;

var_dump($newRequest->get($url, false, true));
*/

$cspReports = DB::query("SELECT * FROM `csp_reports`");

$approvedDomains = DB::query("SELECT * FROM `firewall`");

$cspPolicies = DB::query("SELECT * FROM `users`");

echo SimpleDataGrid::render($cspReports);

echo SimpleDataGrid::render($approvedDomains);

echo SimpleDataGrid::render($cspPolicies);
