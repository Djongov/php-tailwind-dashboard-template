<?php

use Database\MYSQL;
use Api\Output;
use Logs\SystemLog;

if (!isset($_POST['table'],$_POST['id'])) {
    Output::error('Incorrect arguments', 400);
}

if (isset($_SERVER['HTTP_SECRETHEADER'])) {
    if ($_SERVER['HTTP_SECRETHEADER'] !== 'badass') {
        Output::error("Nauhty. You don't know what the secret is", 400);
    }
} else {
    SystemLog::write('A request was sent without the secret header', 'Access');
    Output::error("Nauhty. You are missing a secret", 400);
}

$table = $_POST['table'];

$sql = 'UPDATE `' . $_POST['table'] . '` SET ';

unset($_POST['table']);

$updates = [];
// Check if all keys in $reports_array match the columns
foreach ($_POST as $key => $value) {
    // Add the column to be updated to the SET clause
    $updates[] = "`$key` = ?";
}
// Combine the SET clauses with commas
$sql .= implode(', ', $updates);

// Add a WHERE clause to specify which organization to update
$sql .= " WHERE `id` = ?";

// Prepare and execute the query using queryPrepared
$values = array_values($_POST);
$values[] = $_POST['id']; // Add the username for the WHERE clause

$editRecord = MYSQL::queryPrepared($sql, $values);

if ($editRecord->affected_rows === 0) {
    Output::error('Nothing updated', 409);
} else {
    SystemLog::write('Record id ' . $_POST['id'] . ' edited in ' . $table, 'DataGrid Edit');
    echo Output::success('successfully edited ' . $editRecord->affected_rows . ' records in ' . $table . '');
}
