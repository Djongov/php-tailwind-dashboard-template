<?php

use Template\Forms;
use Template\Html;
use DataGrid\SimpleVerticalDataGrid;
use Logs\SystemLog;
use Security\Firewall;
use Api\Output;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

if (!$isAdmin) {
    SystemLog::write('Got unauthorized for admin page', 'Access');
    Output::error('You are not authorized to view this page', 401);
}

echo HTML::h2('PHP Errors');
$loadErrorFileArray = [
    'inputs' => [
        'hidden' => [
            [
                'name' => 'api-action',
                'value' => 'get-error-file'
            ]
        ]
    ],
    'theme' => $theme,
    'action' => '/api/tools/get-error-file',
    'resultType' => 'html',
    'reloadOnSubmit' => false,
    'submitButton' => [
        'text' => 'Load Error File',
        'size' => 'medium',
    ],
];

$clearErrorFileformArray = [
    'inputs' => [
        'hidden' => [
            [
                'name' => 'api-action',
                'value' => 'clear-error-file'
            ]
        ]
    ],
    'theme' => $theme,
    'action' => '/api/tools/clear-error-file',
    'resultType' => 'html',
    'reloadOnSubmit' => false,
    'submitButton' => [
        'text' => 'Clear Error File',
        'size' => 'medium',
    ],
];
echo '<div class="flex">';
    echo Forms::render($loadErrorFileArray);
    echo Forms::render($clearErrorFileformArray);
echo '</div>';

echo HTML::h2('Server details', true);

echo SimpleVerticalDataGrid::render($_SERVER);

echo HTML::h2('PHP Info', true);

echo phpinfo();
