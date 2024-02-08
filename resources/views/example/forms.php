<?php

use Template\Forms;
use Template\Html;

echo Html::h1('Forms', true);

echo Html::p('This is a forms page. Here is how we can use the form abilities built into the system.', ['text-center']);

echo Html::h2('Form example');

$inputFieldFormOptions = [
    'inputs' => [
        'hidden' => [
            [
                'name' => 'hidden',
                'value' => 'hidden value',
            ]
        ],
    ],
    'action' => '/api/example',
    'submitButton' => [
        'text' => 'Submit',
        'size' => 'large',
    ]
];

echo Forms::render($inputFieldFormOptions);

echo HTML::code('
$inputFieldFormOptions = [
    "inputs" => [
        "hidden" => [
            [
                "name" => "hidden",
                "value" => "hidden value",
            ]
        ],
    ],
    "action" => "/api/example",
    "submitButton" => [
        "text" => "Submit",
        "size" => "large",
    ]
];
');


