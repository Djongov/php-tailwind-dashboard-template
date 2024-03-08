<?php

use Components\Html;
use Components\DataGrid\SimpleVerticalDataGrid;
use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\General;
use App\Security\Firewall;

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
            echo HTML::p('File (' . $file . ') is empty');
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
            // echo '<p>[' . $line . '</p><div class="relative flex py-5 items-center">
            //     <div class="flex-grow border-t border-gray-400"></div>
            //     <span class="flex-shrink mx-4 text-gray-400">Error</span>
            //     <div class="flex-grow border-t border-gray-400"></div>
            // </div>';
            array_push($errorFileArray, $line);
        }
        echo SimpleVerticalDataGrid::render(General::assocToIndexed($errorFileArray));
    } else {
        echo '<p class="red">File (' . $file . ') not readable</p>';
    }
} else {
    echo '<p class="red">File (' . $file . ') does not exist</p>';
}
echo '</div>';
