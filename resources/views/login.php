<?php

use Authentication\AzureAD;
use Authentication\JWT;
use Template\Html;
use Template\Forms;

if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    $idToken = JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME]);
    // Decide whether it is a local login or AzureAD login
    if ($idToken['iss'] === $_SERVER['HTTP_HOST']) {
        // Check if valid
        if (JWT::checkToken($_COOKIE[AUTH_COOKIE_NAME])) {
            header("Location: /dashboard");
        }
    }
    if ($idToken['iss'] === 'https://login.microsoftonline.com/' . Tenant_ID . '/v2.0') {
        // Check if valid
        if (AzureAD::checkJWTToken($_COOKIE[AUTH_COOKIE_NAME])) {
            header("Location: /dashboard");
        }
    }
}

$login_message = 'You are not logged in';

?>
<!-- wrapper div -->
<div class="flex items-center justify-center mx-4">
    <div class="flex flex-col w-full max-w-md my-16 px-4 py-8 bg-gray-50 rounded-lg dark:bg-gray-900 sm:px-6 md:px-8 lg:px-10 border border-gray-300 shadow-md">
        <p class="py-2 px-4 flex justify-center items-center text-red-600"><?= $login_message ?>
        </p>
        <div class="self-center mb-6 text-xl font-light text-gray-600 sm:text-2xl dark:text-white">
            Login To Your Account
        </div>
        <div class="flex gap-4 item-center">
            <a class="mb-4 w-full text-black dark:text-slate-400 font-medium text-center border border-gray-200 rounded-md shadow-sm hover:bg-gray-200 hover:dark:text-black" href="<?= Login_Button_URL ?>
">
                <div class="flex items-center justify-center py-3 px-3 leading-5">
                    <img height="32" width="32" src="/assets/images/MSFT.png" alt="MS Logo" />
                    <span class="ml-3">Sign in with Microsoft</span>
                </div>
            </a>
        </div>
        <?php

        if (LOCAL_USER_LOGIN) :
            echo HTML::p('or login with your local account', 'text-center text-gray-500 mb-4');
            $localLoginForm = [
                'inputs' => [
                    'input' => [
                        // Email
                        [
                            'label' => 'Username',
                            'type' => 'text',
                            'placeholder' => 'John.Doe@example.com',
                            'name' => 'username',
                            'required' => true,
                            'description' => 'Provide a valid username (email)',
                            'id' => uniqid(),
                        ],
                        // Password
                        [
                            'label' => 'Password',
                            'type' => 'password',
                            'name' => 'password',
                            'required' => true,
                            'description' => 'Provide a valid password',
                            'id' => uniqid(),
                        ]
                    ],
                    'hidden' => [
                        [
                            'name' => 'state',
                            'value' => $_GET['destination'] ?? '/'
                        ]
                    ]
                ],
                'theme' => $theme, // Optional, defaults to COLOR_SCHEME
                'method' => 'POST', // Optional, defaults to POST
                'action' => '/auth-verify', // Required
                'button' => 'Login',
                //'redirectOnSubmit' => $_SERVER['QUERY_STRING'] ? '/login?' . $_SERVER['QUERY_STRING'] : '/login',
                //'doubleConfirm' => true,
                //'doubleConfirmKeyWord' => 'delete',
                'resultType' => 'text',
                'buttonSize' => 'small'
            ];

            echo Forms::render($localLoginForm);
        
            //echo '<script src="/assets/js/local-login.js?' . time() . '"></script>';
        endif;
        if (LOCAL_USER_LOGIN) {
            if (MANUAL_REGISTRATION) {
                echo HTML::small('If you do not have an account, please <a class="underline" href="/register">sign up</a>');
            } else {
                echo HTML::small('If you do not have an account, please contact your administrator');
            }
        }
?>
</div>
</div>
