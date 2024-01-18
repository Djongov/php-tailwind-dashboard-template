<?php

use Template\DataGrid;
use Template\Forms;
use Template\Html;

// Provide a form that will allow the user to add a new domain to the CSP approved domains list
echo '<div class="p-4 m-4 max-w-md bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
echo HTML::h2('Add IP', true);
$firewallAddForm = [
    'inputs' => [
        'input' => [
            [
                'type' => 'text',
                'name' => 'cidr',
                'label' => 'CIDR IP',
                'required' => true,
                'placeholder' => '1.1.1.1/32',
                'description' => 'The CIDR IP to add to the firewall.'
            ]
        ]
    ],
    'action' => '/api/admin/firewall/add',
    'submitButton' => [
        'size' => 'medium',
        'text' => 'Add'
    ],
];

echo Forms::render($firewallAddForm, $theme);

echo '</div>';

echo DataGrid::render('firewall', 'Firewall', $theme);
