<?php

use Template\Forms;
use Template\Html;
use Api\Output;
use Logs\SystemLog;
use Template\SimpleDataGrid;

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
    'buttonSize' => 'small',
    'reloadOnSubmit' => false,
    'button' => 'Load Error File',
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
    'buttonSize' => 'small',
    'reloadOnSubmit' => false,
    'button' => 'Clear Error File',
];
echo '<div class="flex">';
    echo Forms::render($loadErrorFileArray);
    echo Forms::render($clearErrorFileformArray);
echo '</div>';

echo HTML::h1('Server details');

//echo SimpleDataGrid::render($_SERVER);
