<?php

use Logs\SystemLog;
use Response\DieCode;

if (!$isAdmin) {
    SystemLog::write('Got unauthorized for admin page', 'Access');
    DieCode::kill('You are not authorized to view this page', 401);
}

use Template\Html;

echo Html::h1('Administration');
