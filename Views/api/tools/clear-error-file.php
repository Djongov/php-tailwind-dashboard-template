<?php declare(strict_types=1);
use App\Api\Response;
use App\Api\Checks;
use App\Security\Firewall;

Firewall::activate();

$checks = new Checks($vars, $_POST);

// Perform the API checks
$checks->apiAdminChecks();

// Awaiting parameters
$allowedParams = ['api-action', 'csrf_token'];

// Check if the required parameters are present
$checks->checkParams($allowedParams, $_POST);

if ($_POST['api-action'] !== 'clear-error-file') {
    Response::output('Invalid action');
}

$file = ini_get('error_log');
if (is_writable($file)) {
    file_put_contents($file, '');
} else {
    echo '<p class="text-red-500 text-bold">File (' . $file . ') not editable</p>';
}
