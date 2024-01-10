<?php

use Authentication\AzureAD;
use Api\Output;

// Make sure that the user who is logged in is the same as the user who is trying to whitelist
if (AzureAD::extractUserName($_COOKIE[AUTH_COOKIE_NAME]) !== $usernameArray['username']) {
    Output::error('Username anomaly', 403);
}

if (isset($_POST['api-action']) && $_POST['api-action'] === 'clear-error-file' && isset($_POST['csrf_token'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<p class="text-red-500 text-bold">Incorrect CSRF token</p>';
        return;
    }
    $file = ini_get('error_log');
    if (is_writable($file)) {
        file_put_contents($file, '');
    } else {
        echo '<p class="text-red-500 text-bold">File (' . $file . ') not editable</p>';
    }
}
