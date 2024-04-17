<?php

use Components\Forms;
use Components\Html;
use App\Security\Firewall;
use Controllers\Api\Output;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

echo HTML::h1('Queries', true);

echo HTML::p('This is a custom query endpoint. You can use this to execute (almost) any query you want.');

$formOptions = [
    'inputs' => [
        'textarea' => [
            [
                'label' => 'Query',
                'name' => 'query',
                'required' => true,
                'value' => 'SELECT blocked_uri, violated_directive, domain, COUNT(*) as Count
FROM csp_reports
GROUP BY blocked_uri, violated_directive, domain
ORDER BY Count DESC;',
                'description' => 'Enter your query here',
                'cols' => 100,
                'rows' => 10,
            ]
        ]
    ],
    'action' => '/api/admin/queries',
    'submitButton' => [
        'text' => 'Submit',
        'size' => 'small',
    ]
];

echo '<div class="p-4 m-4 max-w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
    echo Forms::render($formOptions);
echo '</div>';
