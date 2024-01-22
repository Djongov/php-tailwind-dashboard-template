<?php

use Database\MYSQL;
use Api\Output;
use Api\Checks;
use Logs\SystemLog;
use Template\Html;
use Security\CRSF;

$checks = new Checks($vars);

$checks->checkParams(['table', 'id', 'columns'], $_POST);

// We need the most strict checks for this endpoint
$checks->apiChecks();

$theme = $loginInfoArray['usernameArray']['theme'];

// We will only fetch the columns that we are passed in the request

$selectColumnsArray = explode(',', $_POST['columns']);

// Now let's implode them so that we can use them in the query ``, ``, ``
$selectColumnsString = '`' . implode('`, `', $selectColumnsArray) . '`';

// Check if the columns exist in the database
MYSQL::checkDBColumnsAndTypes($selectColumnsArray, $_POST['table']);

$dataCheck = MYSQL::queryPrepared("SELECT $selectColumnsString FROM `" . $_POST['table'] . "` WHERE `id`=?", [$_POST['id']]);

if ($dataCheck->num_rows > 0) {
    $data_array = $dataCheck->fetch_assoc();
    $html = '';
    $html .= '<div class="mb-6">';
        foreach ($data_array as $key => $value) {
            $html .= '<div class="ml-4 my-2">';
                $html .= '<label for="' . $key . '" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">' . $key . '</label>';
                if (is_int($value)) {
                    $type = 'number';
                } else {
                    $type = 'text';
                }
                $readonly = '';
                $read_only_columns = ['date_created', 'id', 'invited_on', 'created_at', 'created_by', 'client_ip'];
                $input_field_classes = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg outline-none focus:ring-' . $theme . '-500 focus:border-' . $theme . '-500 block w-72 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-' . $theme . '-500 dark:focus:border-' . $theme . '-500';
                if (in_array($key, $read_only_columns)) {
                    $readonly = 'readonly';
                    // override the classes so that the disabled fields are more noticeable
                    $input_field_classes = 'bg-gray-50 border border-red-500 text-gray-900 text-sm rounded-lg outline-none focus:ring-red-600 focus:border-red-700 block w-72 p-2.5 dark:bg-gray-700 dark:border-red-600 dark:placeholder-red-400 dark:text-white dark:focus:ring-red-600 dark:focus:border-red-700';
                }
                if ($value !== null) {
                    $value = htmlentities($value);
                }
                if (strlen($value) > 255) {
                    $html .= HTML::textArea($key, $value, '', $key, '', '', $theme, false, false, false, 10, 50);
                } else {
                    $html .= '<input type="' . $type . '" name="' . $key . '" class="' . $input_field_classes . '" value="' . $value . '" ' . $readonly . ' />';
                }
            $html .= '</div>';
        }
        $html .= '<input type="hidden" name="table" value="' . $_POST['table'] . '" />';
        // Include the CSRF token
        $html .= '<input type="hidden" name="csrf_token" value="' . $_POST['csrf_token'] . '" />';
    $html .= '</div>';
    echo $html;
} else {
    Output::error('No data found', 400);
}
