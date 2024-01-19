<?php

use Api\Output;
use Api\Checks;
use Security\Firewall;

Firewall::activate();

$checks = new Checks($vars);

// Perform the API checks
$checks->apiChecks();

// Awaiting parameters
$allowedParams = ['api-action', 'csrf_token'];

// Check if the required parameters are present
$checks->checkParams($allowedParams, $_POST);

if ($_POST['api-action'] !== 'clear-error-file') {
    Output::error('Invalid action');
}

$file = ini_get('error_log');
if (is_writable($file)) {
    file_put_contents($file, '');
} else {
    echo '<p class="text-red-500 text-bold">File (' . $file . ') not editable</p>';
}
