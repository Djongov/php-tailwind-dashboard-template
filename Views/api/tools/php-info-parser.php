<?php declare(strict_types=1);

use App\Api\Response;
use App\Api\Checks;
use App\Utilities\General;
use App\Security\Firewall;
use Components\DataGrid;

Firewall::activate();

$checks = new Checks($vars, $_POST);

// Perform the API checks
$checks->apiAdminChecks();

// Awaiting parameters
$allowedParams = ['api-action', 'csrf_token'];

// Check if the required parameters are present
$checks->checkParams($allowedParams, $_POST);

if ($_POST['api-action'] !== 'prase-phpinfo') {
    Response::output('Invalid action');
}

$phpInfoArray = General::parsePhpInfo();
echo '<div class="ml-4 dark:text-gray-400">';
    echo DataGrid::fromData('', $phpInfoArray["Features "], $theme);
echo '</div>';
