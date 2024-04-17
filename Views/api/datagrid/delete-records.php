<?php

use App\Database\DB;
use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\Logs\SystemLog;

$checks = new Checks($vars, $_POST);

// Special XOR check for the delete records endpoint
if (!isset($_POST['table'], $_POST['id']) xor isset($_POST['deleteRecords'], $_POST['row'])) {
    Output::error('Incorrect arguments', 400);
}

$checks->apiChecks();

$db = new DB();

$pdo = $db->getConnection();

// If a single delete somes from the button
if (isset($_POST['table'], $_POST['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM `" . $_POST['table'] . "` WHERE `id`=?");
    $stmt->execute([$_POST['id']]);
    if ($stmt->rowCount() === 0) {
        Output::error('No records were deleted', 400);
    }
    $stmt = $pdo->prepare("DELETE FROM `" . $_POST['table'] . "` WHERE `id`=?");
    $stmt->execute([$_POST['id']]);
    if ($stmt->rowCount() > 0) {
        SystemLog::write('Record id ' . $_POST['id'] . ' deleted from ' . $_POST['table'], 'DataGrid Delete');
        echo Output::success('successfully deleted ' . $stmt->rowCount() . ' records');
    }
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

    $stmt = $pdo->prepare($sql);

    $stmt->execute([...$_POST['row']]);
    
    
    if ($stmt->rowCount() === 0) {
        Output::error('No records were deleted', 400);
    }
    if ($stmt->rowCount() > 0) {
        SystemLog::write($stmt->rowCount() . ' records deleted from ' . $_POST['deleteRecords'], 'DataGrid Delete');
        echo Output::success('successfully deleted ' . $stmt->rowCount() . ' records');
    }
} else {
    Output::error('Incorrect arguments', 400);
}
