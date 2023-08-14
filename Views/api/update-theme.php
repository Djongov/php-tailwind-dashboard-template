<?php

use Database\DB;
use Response\DieCode;

$allowed_themes = ['amber', 'green', 'stone', 'rose', 'lime', 'teal', 'sky', 'purple', 'red', 'fuchsia', 'indigo'];

if (isset($_POST['theme']) && in_array($_POST['theme'], $allowed_themes)) {
    if ($loginInfoArray['usernameArray']['username'] === $_POST['username']) {
        $result = DB::queryPrepared("UPDATE `users` SET `theme`=? WHERE `username`=?", [$_POST['theme'], $loginInfoArray['usernameArray']['username']]);
        if ($result) {
            echo "Success";
        } else {
            echo "Update failed.";
        }
    } else {
        DieCode::kill('incorrect user', 400);
    }
    
} else {
    DieCode::kill('incorrect theme', 400);
}
