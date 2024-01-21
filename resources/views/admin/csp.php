<?php

use Template\Forms;
use Template\Html;
use Security\Firewall;
use Api\Output;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

//echo DataGrid::render('csp_approved_domains', 'CSP Approved Domains', $theme);

// Provide a form that will allow the user to add a new domain to the CSP approved domains list
echo '<div class="p-4 m-4 max-w-md bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
echo HTML::h2('Add Domain', true);
$CspApprovedDomainsForm = [
    'inputs' => [
        'input' => [
            [
                'type' => 'text',
                'name' => 'domain',
                'label' => 'Domain',
                'required' => true,
                'placeholder' => 'example.com',
                'description' => 'The domain to add to the CSP approved domains list.'
            ]
        ]
    ],
    'action' => '/api/admin/csp/add',
    'submitButton' => [
        'size' => 'small',
        'text' => 'Add Domain'
    ],
];

echo Forms::render($CspApprovedDomainsForm, $theme);

echo '</div>';

use Database\MYSQL;
use DataGrid\DataGrid;

$users = MYSQL::query('SELECT * FROM `csp_reports`');

$usersData = $users->fetch_all(MYSQLI_ASSOC);

// Create the table
echo DataGrid::createTable('csp_reports', $usersData, $theme);
