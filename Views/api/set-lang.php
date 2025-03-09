<?php

use App\Api\Checks;
use App\Api\Response;


if (MULTILINGUAL) {
    $data = Checks::jsonBody();

    if (isset($data['lang'])) {
        $_SESSION['lang'] = $data['lang'];
    }
    Response::output([]);
}

Response::output('Multilingual is disabled', 400);
