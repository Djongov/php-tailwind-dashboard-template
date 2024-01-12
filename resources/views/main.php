<?php

use Template\DataGrid;
use Template\Html;
use Request\NativeHttp;
use Logs\SystemLog;
use Template\Forms;
use App\General;

use Authentication\Checks;


//var_dump($_SESSION);

//$request = NativeHttp::get('https://www.ipqualityscore.com/api/json/leaked/email/IgJi2FQr2iU11QkbkBtoWun5f1YmSk8y/djongov@gamerz-bg.com', [], true);

// $array = [
//     'to' => [
//         [
//             'email' => 'djongov@gamerz-bg.com',
//             'name' => 'Djo'
//         ]
//     ],
//     'subject' => 'Test',
//     'body' => 'Test body'
// ];

// $headers = [
//     'x-api-key: 57a7c24eb9a17c9b8e0d149586143c0e9c518083185375b8ff994a67b99c4df0'
// ];

// var_dump(NativeHttp::post('https://azure-waf-manager-api.sunwellsolutions.com/v1/mail/send', $array, true, $headers));

echo Html::h1('Welcome h1');
echo Html::h2('Welcome h2');
echo Html::h3('Welcome h3');
echo Html::h4('Welcome h4');

echo Html::p('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed elementum nunc sed risus lacinia tempus. Duis sit amet porta nunc. In a venenatis nulla. Donec eget felis interdum, cursus eros nec, venenatis ipsum. Duis velit orci, imperdiet aliquam vulputate vitae, ultricies et eros. Proin congue finibus sapien in venenatis. Aliquam pulvinar pellentesque leo, ac viverra dolor aliquam non. Pellentesque fringilla eget purus ac convallis. Nam eleifend magna libero, rhoncus consequat enim volutpat nec.');

echo General::currentBrowser();

$form_options = [
    'inputs' => [
        'input' => [
            // Email
            [
                'label' => 'Email Address',
                'type' => 'email',
                'placeholder' => 'qwe',
                'name' => 'email',
                'description' => 'Provide a valid email',
                'id' => uniqid(),
            ],
            // Text
            [
                'label' => 'Text',
                'type' => 'text',
                'name' => 'text',
                'value' => 'qwekqwoekqwokeqwkeokw'
            ],
            // Number
            [
                'label' => 'number', // Optional
                'type' => 'number', // Required, could be text, email, search, password, number
                'name' => 'number', // Required
                'value' => 99, // Optional
                'size' => 'small', // Optional, defaults to medium, possible values are default, small, large
                'min' => 0, // Optional
                'max' => 100, // Optional
                //'step' => 2, // Optional
                'title' => 'This is a title', // Optional
                'disabled' => false,
                'required' => true,
                'readonly' => false,
            ]
        ],
        'checkbox' => [
            [
                'label' => 'Checkbox',
                'type' => 'checkbox',
                'name' => 'foo',
                'value' => 'bar',
                'description' => 'This is an example checkbox',
                'disabled' => false,
                'checked' => true,
                'extraClasses' => ['text-red-500']
            ],
            [
                'label' => 'Checkbox 2',
                'type' => 'checkbox',
                'name' => 'foo2',
                'value' => 'bar2',
                'description' => 'This is an example checkbox 2',
                'disabled' => false,
                'checked' => false,
            ]
        ],
        'checkboxGroup' => [
            [
                'label' => 'Checkbox 1 in group 1',
                'type' => 'checkbox',
                'name' => 'foo',
                'value' => 'bar',
                'description' => 'This is an example checkbox',
                'disabled' => false,
                'checked' => true,
                'group' => 'group1',
                'extraClasses' => ['text-red-500']
            ],
            [
                'label' => 'Checkbox 2 in group 1',
                'type' => 'checkbox',
                'name' => 'foo2',
                'value' => 'bar2',
                'group' => 'group1',
                'description' => 'This is an example checkbox 2',
                'disabled' => false,
                'checked' => false,
            ],
            [
                'label' => 'Checkbox 1 in group 2',
                'type' => 'checkbox',
                'name' => 'foo',
                'value' => 'bar',
                'description' => 'This is an example checkbox',
                'disabled' => false,
                'checked' => true,
                'group' => 'group2',
                'extraClasses' => ['text-red-500']
            ],
            [
                'label' => 'Checkbox 2 in group 2',
                'type' => 'checkbox',
                'name' => 'foo2',
                'value' => 'bar2',
                'group' => 'group2',
                'description' => 'This is an example checkbox 2',
                'disabled' => false,
                'checked' => false,
            ]
        ],
        'toggle' => [
            [
                'label' => 'Toggle',
                'name' => 'toggle',
                'description' => 'This is an example toggle',
                'checked' => true,
                'extraClasses' => ['text-red-500']
            ],
            [
                'label' => 'Toggle',
                'name' => 'toggle2',
                'description' => 'This is an example toggle 1',
                'disabled' => false,
                'checked' => false,
            ]
        ],
        'select' => [
            [
                'label' => 'Select',
                'name' => 'select',
                'options' => [
                    // name ==> value
                    'wqe' => 'wqe',
                    'qwe' => 'qwe'
                ],
                'selected_option' => 'qwe'
            ]
        ],
        'textarea' => [
            [
                'label' => 'Textarea',
                'name' => 'textarea',
                'placeholder' => 'Placeholder',
                'description' => 'This is an example textarea',
                'cols' => 100,
                'rows' => 10,
                'disabled' => false,
                'required' => false,
                'readonly' => false,
                'dataAttributes' => [
                    'test' => 'test',
                    'test2' => 'test2'
                ],
            ]
        ],
        'hidden' => [
            [
                'name' => 'username',
                'value' => $usernameArray['username']
            ],
            [
                'name' => 'name',
                'value' => $usernameArray['name']
            ]
        ],
    ],
    'theme' => $theme, // Optional, defaults to COLOR_SCHEME
    'method' => 'POST', // Optional, defaults to POST
    'action' => '/api/example', // Required
    'additionalClasses' => 'qwerty power', // Optional
    'button' => 'Submit',
    'reloadOnSubmit' => false,
    'confirm' => true,
    'confirmText' => 'Are you sure you want to send this quack?', // Optional, defaults to "Are you sure?" if ommited
    //'doubleConfirm' => true,
    //'doubleConfirmKeyWord' => 'delete',
    'resultType' => 'text',
    'buttonSize' => 'big'
];
echo '<div class="w-[44rem] dark:bg-gray-800 p-2 rounded-md shadow-md shadow-gray-400 my-6">';
    echo Forms::render($form_options);
echo '</div>';

