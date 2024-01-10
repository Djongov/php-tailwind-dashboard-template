<?php

use Api\Output;
use Template\Html;
use Authentication\AzureAD;

if (isset($_POST['api-action']) && $_POST['api-action'] === 'clear-error-file' && isset($_POST['csrf_token']) && ($_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
    Output::error('Incorrect CSRF token', 401);
}

$usernameArray = $loginInfoArray['usernameArray'];

// Make sure that the user who is logged in is the same as the user who is trying to whitelist
if (AzureAD::extractUserName($_COOKIE[AUTH_COOKIE_NAME]) !== $usernameArray['username']) {
    Output::error('Username anomaly', 403);
}


echo '<div class="ml-4 dark:text-gray-400">';

$file = ini_get('error_log');
echo HTML::h3($file);
if (is_file($file)) {
    if (is_readable($file)) {
        if (empty(file($file))) {
            echo HTML::p('File (' . $file . ') is empty');
            return;
        }
        echo HTML::h3(realpath($file));
        $f = file($file);
        $f = implode(PHP_EOL, $f);
        $f = explode(PHP_EOL . '[', $f);
        foreach ($f as $line) {
            if ($line === "") {
                continue;
            }
            echo '<p>[' . $line . '</p><div class="relative flex py-5 items-center">
                <div class="flex-grow border-t border-gray-400"></div>
                <span class="flex-shrink mx-4 text-gray-400">Error</span>
                <div class="flex-grow border-t border-gray-400"></div>
            </div>';
        }
    } else {
        echo '<p class="red">File (' . $file . ') not readable</p>';
    }
} else {
    echo '<p class="red">File (' . $file . ') does not exist</p>';
}
echo '</div>';
