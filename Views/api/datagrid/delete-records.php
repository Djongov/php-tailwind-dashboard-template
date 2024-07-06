<?php declare(strict_types=1);
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

    // Step 2: Delete the record
    $stmt = $pdo->prepare("DELETE FROM " . $_POST['table'] . " WHERE id = ?");
    try {
        $stmt->execute([$_POST['id']]);
        SystemLog::write('Record with id ' . $_POST['id'] . ' deleted from ' . $_POST['table'], 'DataGrid Delete');
        echo Output::success('Successfully deleted the record with id ' . $_POST['id'] . ' from ' . $_POST['table']);
    } catch (\PDOException $e) {
        Output::error('Failed to delete the record', 400);
    }
} elseif (isset($_POST['deleteRecords'], $_POST['row'])) {

    $ids = implode(',', array_fill(0, count($_POST['row']), '?'));

    // Construct the DELETE statement
    $sql = "DELETE FROM " . $_POST['deleteRecords'] . " WHERE id IN ($ids)";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute($_POST['row']);
        SystemLog::write('Records with ids ' . implode(',', $_POST['row']) . ' deleted from ' . $_POST['deleteRecords'], 'DataGrid Delete');
        echo Output::success('Successfully deleted the records');
    } catch (\PDOException $e) {
        Output::error('Failed to delete the records', 400);
    }
       
} else {
    Output::error('Incorrect arguments', 400);
}
