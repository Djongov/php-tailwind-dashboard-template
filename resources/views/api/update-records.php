<?php

use Database\MYSQL;
use Response\DieCode;
use Logs\SystemLog;

if (!isset($_POST['table'],$_POST['id'])) {
    DieCode::kill('Incorrect arguments', 400);
}

if (isset($_SERVER['HTTP_SECRETHEADER'])) {
    if ($_SERVER['HTTP_SECRETHEADER'] !== 'badass') {
        DieCode::kill("Nauhty. You don't know what the secret is", 400);
    }
} else {
    SystemLog::write('A request was sent without the secret header', 'Access');
    DieCode::kill("Nauhty. You are missing a secret", 400);
}

$sql = "UPDATE `" . $_POST['table'] . "` SET ";

$index = 0;
foreach ($_POST as $column => $value) {
    $index++;
    // Skip `table` from the query
    if ($column === 'table') {
        continue;
    }
    // Sometimes JS converts numbers to strings
    if (is_numeric($value)) {
        $value = (int) $value;
    }
    // On first iteration we don't need to start with a comma
    if ($index === 1) {
        $sql .= ($value === '') ? '`' . $column . '`= NULL' : '`' . $column . '`=\'' . addcslashes($value, "'") . '\'';
        continue;
    }
    // And comma as a start on all other iterations
    $sql .= ($value === '') ? ',`' . $column . '`= NULL' : ',`' . $column . '`=\'' . addcslashes($value, "'") . '\'';
}
// And finish off the rest of the query
$sql .= ' WHERE `id`=\'' . $_POST['id'] . '\'';

MYSQL::query($sql);

echo 'Success';
