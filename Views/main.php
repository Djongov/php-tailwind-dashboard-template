<?php

use Template\DataGrid;
use Template\DataGridQuery;
use Template\Html;
use Request\Http;

/*
$url = 'http://ip-api.com/json/';

use \Request\Http;

$newRequest = new Http;

var_dump($newRequest->get($url, false, true));
*/

echo Html::h1('Welcome');
echo Html::h2('Welcome');
echo Html::p('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed elementum nunc sed risus lacinia tempus. Duis sit amet porta nunc. In a venenatis nulla. Donec eget felis interdum, cursus eros nec, venenatis ipsum. Duis velit orci, imperdiet aliquam vulputate vitae, ultricies et eros. Proin congue finibus sapien in venenatis. Aliquam pulvinar pellentesque leo, ac viverra dolor aliquam non. Pellentesque fringilla eget purus ac convallis. Nam eleifend magna libero, rhoncus consequat enim volutpat nec.');
echo Html::p(Http::get('https://api.ipbase.com/v2/info', false, false), 'p-4 bg-gray-100 dark:bg-gray-700 rounded-lg m-4 text-' . $theme . '-500');
echo DataGridQuery::render('csp_reports', 'CSP Reports', 'SELECT `id`, `domain`, `url`, `referrer`, `violated_directive`, `effective_directive`, `disposition`, `blocked_uri`, `source_file`, `status_code`, `script_sample` FROM `csp_reports`', $theme);

echo DataGrid::render('csp_reports', 'CSP Reports', $theme);

echo DataGrid::render('firewall', 'Firewall', $theme);

echo DataGrid::render('users', 'Users', $theme);
