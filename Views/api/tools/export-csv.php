<?php declare(strict_types=1);

use Controllers\Api\Checks;

$checks = new Checks($vars, $_POST);

$checks->apiChecksNoCSRFHeader(false);

if (isset($_POST['data'])) {
    $data = $_POST['data'];
} else {
    $data = '';
}

$data = unserialize($data);

if (isset($_POST['type'])) {
    $type = htmlspecialchars($_POST['type']);
} else {
    $type = '';
}

// file name for download
$fileName = $type . "-data-" . date('Y-m-d-H-i-s') . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $fileName . '"');

$output = fopen('php://output', 'w');

// Assuming that the first element of $data contains the keys (column names)
if (!empty($data)) {
    $columnNames = array_keys(reset($data));
    fputcsv($output, $columnNames, ";");
}

foreach ($data as $row) {
    fputcsv($output, $row, ";");
}

fclose($output);
exit;
