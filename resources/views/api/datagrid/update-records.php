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

// Because the POST data comes from a fetch request, it serializes the data and everything comes through as a string which could lead to DB query errors. Let's convert the data to the correct types
foreach ($_POST as $key => &$value) {
    if (is_numeric($value)) {
        $value = intval($value);
    // Also, if the value is an empty string, let's convert it to null
    } elseif ($value === '') {
        $value = null;
    }
}

$sql = 'UPDATE `' . $_POST['table'] . '` SET ';

unset($_POST['table']);

MYSQL::checkDBColumnsAndTypes($_POST, $table);

// // Run the DESCRIBE query and fetch the result
// $result = MYSQL::query("DESCRIBE csp_reports;");

// // Fetch the rows from the result
// $rows = $result->fetch_all(MYSQLI_ASSOC);

// // Print out the result
// return var_dump($rows);

$updates = [];

// Check if all keys in $_POST match the columns
foreach ($_POST as $key => $value) {
    if ($value === '') {
        // Set to NULL or a default value as needed
        $updates[] = "`$key` = NULL"; // or "`$key` = DEFAULT"
    } else {
        $updates[] = "`$key` = ?";
    }
}
// Combine the SET clauses with commas
$sql .= implode(', ', $updates);

// Add a WHERE clause to specify which organization to update
$sql .= " WHERE `id` = ?";

// Prepare and execute the query using queryPrepared
$values = array_values($_POST);

$values[] = $_POST['id']; // Add the 'id' for the WHERE clause

$editRecord = MYSQL::queryPrepared($sql, $values);


if ($editRecord->affected_rows === 0) {
    Output::error('Nothing updated', 409);
} else {
    SystemLog::write('Record id ' . $_POST['id'] . ' edited in ' . $table, 'DataGrid Edit');
    echo Output::success('successfully edited ' . $editRecord->affected_rows . ' records in ' . $table . '');
}
