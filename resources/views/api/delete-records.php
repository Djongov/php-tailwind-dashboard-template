<?php

use Database\MYSQL;
use Response\DieCode;
use Logs\SystemLog;

if (!isset($_POST['table'], $_POST['id']) xor isset($_POST['deleteRecords'], $_POST['row'])) {
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

// If a single delete somes from the button
if (isset($_POST['table'], $_POST['id'])) {
    MYSQL::queryPrepared("DELETE FROM `" . $_POST['table'] . "` WHERE `id`=?", $_POST['id']);
    SystemLog::write('Record id ' . $_POST['id'] . ' deleted from ' . $_POST['table'], 'Delete Record');
    echo 'Success';
// Or mass delete from the Delete selected button
} elseif (isset($_POST['deleteRecords'], $_POST['row'])) {
    $ids = '';
    foreach ($_POST['row'] as $index => $id) {
        if ($index === 0) {
            $ids .= '?';
        } else {
            $ids .= ',?';
        }
    }

    $sql = "DELETE FROM `" . $_POST['deleteRecords'] . "` WHERE `id` IN ($ids)";
    
    MYSQL::queryPrepared($sql, [...$_POST['row']]);
    SystemLog::write(count($_POST['row']) . ' records deleted from ' . $_POST['deleteRecords'], 'Delete Record');
    echo 'Success';
} else {
    DieCode::kill('Incorrect arguments', 400);
}
