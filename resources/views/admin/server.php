<?php

use Template\Forms;
use Template\Html;
use Api\Output;
use DataGrid\SimpleVerticalDataGrid;
use Logs\SystemLog;
use Template\DataGrid;

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
    'action' => '/api/get-error-file',
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
    'action' => '/api/clear-error-file',
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

echo HTML::h2('Server details');

echo SimpleVerticalDataGrid::render($_SERVER);
