<?php

use App\Database\DB;
use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\Logs\SystemLog;
use Components\Html;
use App\Database\CSRF;

$checks = new Checks($vars, $_POST);

$checks->checkParams(['table', 'id', 'columns'], $_POST);

// We need the most strict checks for this endpoint
$checks->apiChecks();

$theme = $loginInfoArray['usernameArray']['theme'];

// We will only fetch the columns that we are passed in the request

$selectColumnsArray = explode(',', $_POST['columns']);

// Now let's implode them so that we can use them in the query ``, ``, ``
$selectColumnsString = '`' . implode('`, `', $selectColumnsArray) . '`';

$db = new DB();

// Check if the columns exist in the database
$db->checkDBColumnsAndTypes($selectColumnsArray, $_POST['table']);

$pdo = $db->getConnection();

$stmt = $pdo->prepare("SELECT $selectColumnsString FROM `" . $_POST['table'] . "` WHERE `id`=?");

$stmt->execute([$_POST['id']]);


$dataTypes = $db->describe($_POST['table']);

if ($stmt->rowCount() > 0) {
    $data_array = $stmt->fetch(\PDO::FETCH_ASSOC);
    $html = '';
    $html .= '<div class="mb-6">';
        foreach ($data_array as $key => $value) {
            $html .= '<div class="ml-4 my-2">';
                // First sanitize the value
                if ($value !== null) {
                    $value = htmlentities($value);
                }
                // Decide whether a field is disabled or not
                $read_only_columns = ['date_created', 'id', 'invited_on', 'created_at', 'created_by', 'client_ip'];
                if (in_array($key, $read_only_columns)) {
                    $readonly = true;
                } else {
                    $readonly = false;
                }

                // Now let's map the data types to the input types
                if ($dataTypes[$key] === 'bool') {
                    $html .= HTML::toggleCheckBox(uniqid(), $key, $value, $key, ($value === 1 || $value === "1") ? true : false, $theme, $readonly);
                }
                if ($dataTypes[$key] === 'int') {
                    $html .= HTML::input('default', 'number', uniqid(), $key, $key, $value, '', '', $key, $theme, false, true, ($readonly) ? true : false);
                }
                if ($dataTypes[$key] === 'float') {
                    $html .= HTML::input('default', 'number', uniqid(), $key, $key, $value, '', '', $key, $theme, false, true, ($readonly) ? true : false);
                }
                if ($dataTypes[$key] === 'datetime') {
                    $html .= HTML::input('default', 'datetime-local', uniqid(), $key, $key, $value, '', '', $key, $theme, false, true, ($readonly) ? true : false);
                }
                if ($dataTypes[$key] === 'string') {
                    if ($key === 'password') {
                        $html .= HTML::input('default', 'password', uniqid(), $key, $key, $value, '', 'This is most likely a hashed value of the password', $key, $theme, false, true, ($readonly) ? true : false);
                    } elseif ($value !== null && strlen($value) > 255) {
                        $html .= HTML::textArea(null, $key, $value, '', $key, '', '', $theme, false, false, false, 10, 50);
                    } else {
                        $html .= HTML::input('default', 'text', uniqid(), $key, $key, $value, '', '', $key, $theme, false, true, ($readonly) ? true : false);
                    }
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
