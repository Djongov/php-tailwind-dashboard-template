<?php

use Api\Output;
use Api\Checks;
use Database\MYSQL;
use Security\Firewall;

Firewall::activate();

$checks = new Checks($vars, $_POST);

// Perform the API checks
$checks->apiAdminChecks();

// Awaiting parameters
$allowedParams = ['cidr', 'csrf_token'];

// Check if the required parameters are present
$checks->checkParams($allowedParams, $_POST);

// Now that we are sure that the general checks have passed, we can proceed with the actual logic

// Check if the provided IP is in CIDR notation
if (!str_contains($_POST['cidr'], '/')) {
    Output::error('Invalid CIDR notation');
}

// Explode the CIDR notation
$cidr = explode('/', $_POST['cidr']);

// Check if the provided IP is valid
if (!filter_var($cidr[0], FILTER_VALIDATE_IP)) {
    Output::error('Invalid IP');
}

// Check if the provided CIDR is valid
if (!filter_var($cidr[1], FILTER_VALIDATE_INT)) {
    Output::error('Invalid CIDR');
}

// Check if the provided CIDR is in range
if ($cidr[1] < 1 || $cidr[1] > 32) {
    Output::error('Invalid CIDR');
}

// Strip the csrf_token from the POST array and check if we have such a column in the firewall table
unset($_POST['csrf_token']);

// Check if the provided IP is already in the firewall table
$checkFirewallIp = MYSQL::queryPrepared('SELECT * FROM `firewall` WHERE `ip_cidr` = ?', [$_POST['cidr']]);
if ($checkFirewallIp->num_rows === 1) {
    Output::error('IP already in firewall');
}

// Now that we can be sure that the provided IP is not in DB, let's save to DB but also check if the comment is set
if (isset($_POST['comment'])) {
    $addFirewallIp = MYSQL::queryPrepared('INSERT INTO `firewall` (`ip_cidr`, `created_by`, `comment`) VALUES (?,?,?)', [$_POST['cidr'], $vars['usernameArray']['username'], htmlspecialchars($_POST['comment'])]);
} else {
    $addFirewallIp = MYSQL::queryPrepared('INSERT INTO `firewall` (`ip_cidr`, `created_by`) VALUES (?,?)', [$_POST['cidr'], $vars['usernameArray']['username']]);
}

// Let's check if the query was successful
if ($addFirewallIp->affected_rows === 1) {
    echo Output::success('IP added to firewall');
} else {
    Output::error('Failed to add IP to firewall');
}

