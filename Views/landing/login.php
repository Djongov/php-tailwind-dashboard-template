<?php declare(strict_types=1);

use App\Authentication\Azure\AzureAD;
use App\Authentication\JWT;
use App\Authentication\AuthToken;
use Components\Html;
use Components\Forms;
use Components\Alerts;

$destinationUrl = $_GET['destination'] ?? '/';
if ($destinationUrl === '/logout') {
    $destinationUrl = '/';
}

if (AuthToken::get() !== null) {
    $idToken = JWT::parseTokenPayLoad(AuthToken::get());
    // Decide whether it is a local login or AzureAD login
    if ($idToken['iss'] === $_SERVER['HTTP_HOST']) {
        // Check if valid
        if (JWT::checkToken(AuthToken::get())) {
            header('Location: ' . $destinationUrl);
        }
    }
    if ($idToken['iss'] === 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/v2.0') {
        // Check if valid
        if (AzureAD::check(AuthToken::get())) {
            header('Location: ' . $destinationUrl);
        }
    }
}

echo '<div class="flex items-center justify-center mx-4">';
    echo '<div class="flex flex-col w-full max-w-md my-16 px-4 py-6 rounded-lg ' . LIGHT_COLOR_SCHEME_CLASS . ' ' . DARK_COLOR_SCHEME_CLASS . ' sm:px-6 md:px-8 lg:px-10 border border-gray-300 shadow-md">';
        // Now the different external provider login options
        if (AZURE_AD_LOGIN || MICROSOFT_LIVE_LOGIN || GOOGLE_LOGIN) {
            echo Html::h3('Login with a provider account', true, ['my-6']);
        }
        // Azure
        if (AZURE_AD_LOGIN) {
            echo '<div class="flex gap-4 item-center">';
                echo '<a class="mb-4 w-full ' . TEXT_COLOR_SCHEME . ' ' . TEXT_DARK_COLOR_SCHEME . ' font-medium text-center border border-gray-200 rounded-md shadow-sm hover:bg-gray-200 hover:dark:text-black" href="' . AZURE_AD_LOGIN_BUTTON_URL . '">';
                    echo '<div class="flex items-center justify-center py-3 px-3 leading-5">';
                        echo MS_LOGO;
                            echo '<span class="ml-3">Sign in with Microsoft Work or school</span>';
                    echo '</div>';
                echo '</a>';
            echo '</div>';
        }
        if (MICROSOFT_LIVE_LOGIN) {
            echo '<div class="flex gap-4 item-center">';
                echo '<a class="mb-4 w-full ' . TEXT_COLOR_SCHEME . ' ' . TEXT_DARK_COLOR_SCHEME . ' font-medium text-center border border-gray-200 rounded-md shadow-sm hover:bg-gray-200 hover:dark:text-black" href="' . MS_LIVE_LOGIN_BUTTON_URL . '">';
                    echo '<div class="flex items-center justify-center py-3 px-3 leading-5">';
                        echo MS_LOGO;
                        echo '<span class="ml-3">Sign in with Microsoft live account</span>';
                    echo '</div>';
                echo '</a>';
            echo '</div>';
        }
        // Google
        if (GOOGLE_LOGIN) {
            echo '<div class="flex gap-4 item-center">';
                echo '<a class="mb-4 w-full ' . TEXT_COLOR_SCHEME . ' ' . TEXT_DARK_COLOR_SCHEME . ' font-medium text-center border border-gray-200 rounded-md shadow-sm hover:bg-gray-200 hover:dark:text-black" href="' . GOOGLE_LOGIN_BUTTON_URL . '">';
                    echo '<div class="flex items-center justify-center py-3 px-3 leading-5">';
                        echo GOOGLE_LOGO;
                        echo '<span class="ml-3">Sign in with Google</span>';
                    echo '</div>';
                echo '</a>';
            echo '</div>';
        }
        // Local login
        if (LOCAL_USER_LOGIN) {
            if (AZURE_AD_LOGIN || GOOGLE_LOGIN || MICROSOFT_LIVE_LOGIN) {
                echo Html::p('or login with your local account', ['text-center', 'mb-4']);
            } elseif (!AZURE_AD_LOGIN && !GOOGLE_LOGIN && !MICROSOFT_LIVE_LOGIN) {
                echo Html::h3('Login with your local account', true, ['my-6']);
            }
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
                    'checkbox' => [
                        [
                            'label' => 'Remember me',
                            'name' => 'remember',
                        ]
                    ],
                    'hidden' => [
                        [
                            'name' => 'state',
                            'value' => ($destinationUrl !== '/' && (substr($destinationUrl, 0, 1) !== '/' || !in_array($destinationUrl, ['/login', '/logout']))) ? '/' : $destinationUrl
                        ]
                    ]
                ],
                'theme' => $theme,
                'method' => 'POST',
                'action' => '/auth/local',
                'id' => 'local-login-form',
                'resultType' => 'text',
                'submitButton' => [
                    'text' => 'Login',
                    'size' => 'medium',
                ],
            ];

            echo Forms::render($localLoginForm);

            echo '<script src="/assets/js/local-login.js?' . time() . '"></script>';
        }
        if (LOCAL_USER_LOGIN) {
            if (MANUAL_REGISTRATION) {
                echo Html::small('If you do not have an account, please <a class="underline" href="/register">sign up</a>');
            } else {
                echo Html::small('If you do not have an account, please contact your administrator');
            }
        }
        if (!LOCAL_USER_LOGIN && !AZURE_AD_LOGIN && !GOOGLE_LOGIN && !MICROSOFT_LIVE_LOGIN) {
            echo Alerts::danger('No login methods are enabled. Check config');
}
    echo '</div>';
    echo '</div>';
echo '</div>';
