<?php

use Components\Html;
use Components\Alerts;
use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\Utilities\General;
use App\Security\Firewall;
use Components\DataGrid;
use Google\Service\Adsense\Alert;

Firewall::activate();

$checks = new Checks($vars, $_POST);

// Perform the API checks
$checks->apiAdminChecks();

// Awaiting parameters
$allowedParams = ['api-action', 'csrf_token'];

// Check if the required parameters are present
$checks->checkParams($allowedParams, $_POST);

if ($_POST['api-action'] !== 'get-error-file') {
    Output::error('Invalid action');
}

echo '<div class="ml-4 dark:text-gray-400">';

$file = ini_get('error_log');
if (is_file($file)) {
    if (is_readable($file)) {
        if (empty(file($file))) {
            echo Alerts::danger('File (' . $file . ') is empty');
            return;
        }
        echo HTML::h3(realpath($file));
        $errorFileArray = [];
        $f = file($file);
        $f = implode(PHP_EOL, $f);
        $f = explode(PHP_EOL . '[', $f);
        foreach ($f as $line) {
            if ($line === "") {
                continue;
            }
            // </div>';
            array_push($errorFileArray, $line);
        }
        $errorFileArray = General::assocToIndexed($errorFileArray);
        echo DataGrid::fromData('error-file (' . $file . ')', $errorFileArray, $theme);
    } else {
         echo Alerts::danger('File (' . $file . ') not readable');
    }
} else {
    echo Alerts::danger('File (' . $file . ') does not exist');
}
echo '</div>';
