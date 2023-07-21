<?php

use Template\DataGrid;
use Template\DataGridQuery;
use Template\Html;
use Request\Http;
use Logs\SystemLog;

/*
$url = 'http://ip-api.com/json/';

use \Request\Http;

$newRequest = new Http;

var_dump($newRequest->get($url, false, true));
*/


echo Html::h1('Welcome h1');
echo Html::h2('Welcome h2');
echo Html::h3('Welcome h3');
echo Html::h4('Welcome h4');

echo Html::p('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed elementum nunc sed risus lacinia tempus. Duis sit amet porta nunc. In a venenatis nulla. Donec eget felis interdum, cursus eros nec, venenatis ipsum. Duis velit orci, imperdiet aliquam vulputate vitae, ultricies et eros. Proin congue finibus sapien in venenatis. Aliquam pulvinar pellentesque leo, ac viverra dolor aliquam non. Pellentesque fringilla eget purus ac convallis. Nam eleifend magna libero, rhoncus consequat enim volutpat nec.');

#echo Html::p(Html::code(Http::get('https://api.ipbase.com/v2/info', false, false)));

echo Html::code('
{
    "name":"John",
    "age":30,
    "car":null
}
', 'Example JSON');

echo DataGridQuery::render('csp_reports', 'CSP Reports', 'SELECT `id`, `created_at`, `domain`, `url`, `referrer`, `violated_directive`, `effective_directive`, `disposition`, `blocked_uri`, `source_file`, `status_code`, `script_sample` FROM `csp_reports`', $theme);

echo DataGrid::render('system_log', 'System Log', $theme);

echo DataGrid::render('firewall', 'Firewall', $theme);

echo DataGrid::render('users', 'Users', $theme);
