<?php

use App\GeneralMethods;
use Template\DataGrid;
use Template\Html;
use Request\Http;
use Logs\SystemLog;
use Template\Forms;

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

echo Html::p(GeneralMethods::randomString());

$form_options = [
    'inputs' => [
        'input' => [
            [
                'label_name' => 'Email Address',
                'input_type' => 'email',
                'placeholder' => 'qwe',
                'name' => 'email',
                'description' => 'Provide a valid email',
                'disabled' => false,
                'required' => false,
                'readonly' => false,
            ],
            [
                'label_name' => 'Text',
                'input_type' => 'text',
                'name' => 'text',
            ]
        ],
        'checkbox' => [
            [
                'label_name' => 'Checkbox',
                'input_type' => 'checkbox',
                'placeholder' => '',
                'name' => 'checkbox',
                'value' => 'foo',
                'description' => 'This is an example checkbox',
                'disabled' => false,
                'required' => false,
                'checked' => false,
            ]
        ],
        'select' => [
            [
                'label_name' => 'Select',
                'name' => 'select',
                'options' => [
                    // name ==> value
                    'wqe' => 'wqe',
                    'qwe' => 'qwe'
                ],
                'selected_option' => 'qwe'
            ]
        ],
        'hidden' => [
            [
                'name' => 'username',
                'value' => $usernameArray['username']
            ]
        ],
    ],
    'theme' => $theme, // Optional, defaults to COLOR_SCHEME
    'action' => '/api/example',
    'button' => 'Submit',
    'reloadOnSubmit' => false,
    'confirm' => true,
    'confirmText' => 'Are you sure you want to send it?',
    'resultType' => 'html',
    'buttonSize' => 'medium'
];

echo Forms::render($form_options);

echo Html::code(json_encode($form_options, JSON_PRETTY_PRINT), 'Form Structure');
