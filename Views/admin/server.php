<?php declare(strict_types=1);

use Components\Forms;
use Components\Html;
use App\Logs\SystemLog;
use App\Security\Firewall;
use Controllers\Api\Output;
use Components\DataGrid;
use Components\Alerts;

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

// First check if error_log is there
$errorLog = ini_get('error_log');

if (empty($errorLog)) {
    echo HTML::h2('Error Log', true);
    echo Alerts::danger('No error log file is set in php.ini');
} else {
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

}


echo DataGrid::fromData('Server Info', $_SERVER, $theme);

echo HTML::h2('PHP Info', true);

$phpInfoFormOptions = [
    'inputs' => [
        'hidden' => [
            [
                'name' => 'api-action',
                'value' => 'prase-phpinfo'
            ]
        ]
    ],
    'theme' => $theme,
    'action' => '/api/tools/php-info-parser',
    'resultType' => 'html',
    'reloadOnSubmit' => false,
    'submitButton' => [
        'text' => 'Get PHP Info',
        'size' => 'medium',
    ],
];

echo Forms::render($phpInfoFormOptions);
