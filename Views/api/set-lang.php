<?php

use App\Api\Checks;
use App\Api\Response;

$data = Checks::jsonBody();

if (isset($data['lang'])) {
    $_SESSION['lang'] = $data['lang'];
}


Response::output([]);
// $location = $_SERVER['HTTP_REFERER'] ?? '/';
// // Redirect back to the previous page
// header("Location: " . $location);
// exit;
