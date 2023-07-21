<?php

use Database\DB;
use Response\DieCode;

if (!isset($_POST['table'],$_POST['id'])) {
    DieCode::kill('Incorrect arguments', 400);
}

$theme = $loginInfoArray['usernameArray']['theme'];

$dataCheck = DB:: queryPrepared("SELECT * FROM `" . $_POST['table'] . "` WHERE `id`=?", [$_POST['id']]);

if ($dataCheck->num_rows > 0) {
    $data_array = $dataCheck->fetch_assoc();
    //echo json_encode($data_array);
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
        $input_field_classes = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-' . $theme . '-500 focus:border-' . $theme . '-500 block w-72 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-' . $theme . '-500 dark:focus:border-' . $theme . '-500';
        if (in_array($key, $read_only_columns)) {
            $readonly = 'readonly';
            // override the classes so that the disabled fields are more noticeable
            $input_field_classes = 'bg-gray-50 border border-red-500 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-700 block w-72 p-2.5 dark:bg-gray-700 dark:border-red-600 dark:placeholder-red-400 dark:text-white dark:focus:ring-red-600 dark:focus:border-red-700';
        }
        if ($value !== null) {
            $value = htmlentities($value);
        }
        $html .= '<input type="' . $type . '" name="' . $key . '" class="' . $input_field_classes . '" value="' . $value . '" ' . $readonly . ' />';
        $html .= '</div>';
    }
    $html .= '<input type="hidden" name="db_table" value="' . $_POST['table'] . '" />';
    $html .= '</div>';
    echo $html;
} else {
    echo 'No data found';
}
