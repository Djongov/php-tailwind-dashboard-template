<?php

use Database\DB;

use App\GeneralMethods;

use Authentication\AzureAD;
use Template\Forms;
use Template\Html;

$allowed_themes = ['amber', 'green', 'stone', 'rose', 'lime', 'teal', 'sky', 'purple', 'red', 'fuchsia', 'indigo'];

$locale = (isset($usernameArray['origin_country'])) ? GeneralMethods::country_code_to_locale($usernameArray['origin_country']) : null;

echo '<div class="p-4 m-4 max-w-fit bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700 text-black dark:text-slate-300">';
    echo Html::h2('User settings');
    echo '<table class="w-auto">';
    foreach ($usernameArray as $name => $setting) {
        echo '<tr>';
        if ($name === 'id') {
            continue;
        }
        // Check if date
        if ($setting !== null && strtotime($setting)) {
            $fmt = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::GREGORIAN);
            echo ' <td class="w-full"><strong>' . $name . '</strong> : ' . $fmt->format(strtotime($setting)) . '  </td>';
            continue;
        }
        if ($name === 'theme') {
            echo '<td class="w-full"><div class="flex my-2"><strong>' . $name . '</strong> : ';
            $themeOptions = [];
            $currentTheme = '';
            foreach ($allowed_themes as $color) {
                $themeOptions[$color] = $color;
                if ($theme === $color) {
                    $currentTheme = $color;
                }
            }
            $updateThemeOptioms = [
                'inputs' => [
                    'select' => [
                        'select' => [
                            'label_name' => '',
                            'name' => 'theme',
                            'options' => $themeOptions,
                            'selected_option' => $currentTheme
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
                'action' => '/api/update-theme',
                'buttonSize' => 'small',
                'button' => 'Update'
            ];
            echo Forms::render($updateThemeOptioms);
            continue;
        }
        // Only show copy to clipboard on non-null items
        $copyToClipboard = ($setting === null) ? '' : 'c0py';
        echo ' <td class="w-full"><strong>' . $name . '</strong> : <span class="' . $copyToClipboard . '">' . $setting . '</span>  </td>';
        echo '</tr>';
    }
    echo '</table>';

    echo '<p>Here is your session info:</p>';
    echo '<p><strong>Token expiry: </strong>' . $fmt->format(strtotime(date("Y-m-d H:i:s", substr(AzureAD::parseJWTTokenPayLoad($_COOKIE['auth_cookie'])['exp'], 0, 10)))) . '</p>';
    echo '<p><strong>Token: </strong></p><p class="break-all c0py">' . $_COOKIE['auth_cookie'] . '</p>';
    
echo '</div>';

if (empty($usernameArray['email'])) {
    echo '<div class="p-4 m-4 max-w-lg bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
    echo '<div class="flex flex-row flex-wrap items-center mb-4">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline-block w-6 h-6 fill-amber-500">
                <title>Missing Email</title>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>';
    echo Html::h2('Missing Email');
    echo '</div>';
    echo '<p>We noticed that we haven\'t got your email address from your token claims. We will try to email you on your username which is <strong>' . $usernameArray['username'] . '</strong> but in case you think you can\'t be receiving emails on your username address, here you can give an aleternative. Don\'t worry, we will only be sending important notifications. Signups for newsletters and others are separate.</p>';

    $updateEmailFormOptions = [
        'inputs' => [
            'input' => [
                [
                    'label_name' => 'Email Address',
                    'input_type' => 'email',
                    'input_placeholder' => '',
                    'name' => 'email',
                    'description' => 'provide a valid email',
                    'disabled' => false,
                    'required' => true,
                ]
            ],
            'hidden_inputs' => [
                [
                    'input_name' => 'username',
                    'input_value' => $usernameArray['username']
                ]
            ],
        ],
        'theme' => $theme,
        'action' => '/api/update-email-address',
        'button' => 'Update'
    ];

    echo Forms::render($updateEmailFormOptions);

    echo '</div>';
}
echo '<div class="p-4 m-4 max-w-fit bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
echo Html::h2('Forget About me');
echo '<p>This will delete your account in our database along with any data we have about your account.</p>';
echo '<form id="delete-user-form">';
echo '<button id="delete-user-trigger" class="my-6 py-2 px-4 bg-red-600 hover:bg-red-700 focus:ring-red-500 focus:ring-offset-red-200 text-white w-64 h-18 transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg" type="button" data-modal-toggle="delete-user">
        Delete User
    </button>';
echo
'<div id="delete-user" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 md:inset-0 h-modal md:h-full justify-center items-center" aria-hidden="true">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="delete-user">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-6 text-center">
                    <svg aria-hidden="true" class="mx-auto mb-4 w-14 h-14 text-gray-400 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 id="delete-user-text" class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete your user?</h3>
                    <p class="mb-4">

                    This will delete your username from our database. This will also remove you from organization where you are a member. This will NOT remove any logs that have your name in it. Your user will be re-created if you login again to the app.

                    </p>
                    <button data-modal-toggle="delete-user" type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                        Yes, I\'m sure
                    </button>
                    <button data-modal-toggle="delete-user" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancel</button>
                </div>
            </div>
        </div>
    </div>
    ';
// Close the mass delete form
echo '<input type="hidden" name="deleteUser" value="' . $usernameArray['username'] . '" />';
echo '</form>';

echo '</div>';
