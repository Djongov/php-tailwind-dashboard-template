<?php

use Api\Output;
use Database\MYSQL;
use Authentication\Checks;

$checks = new Checks();
$checks->genericChecks($vars);
// Let's pick up the username from the user id from the DB
$userInfo = MYSQL::queryPrepared('SELECT `username` FROM `users` WHERE `id`=?', [$routeInfo[2]['id']]);
if ($userInfo->num_rows === 0) {
    echo Output::error('User not found');
}

// Now check if the user submitting this is the same as the user being deleted
if ($vars['usernameArray']['id'] !== intval($routeInfo[2]['id'])) {
    echo Output::error('You are not allowed to edit this user');
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Secret header checks
    $checks->checkSecretHeader();
    // Let's catch php input stream
    $putData = file_get_contents('php://input');
    // Now we need to get the put data and make into array
    $putData = json_decode($putData, true);
    // CSRF check
    $checks->checkCSRF($putData['csrf_token']);
    // Remove from putData array csrf_token
    unset($putData['csrf_token']);
    $sql = 'UPDATE `users` SET ';
    $updates = [];
    // Check if all keys in $array match the columns
    foreach ($putData as $key => $value) {
        // Add the column to be updated to the SET clause
        $updates[] = "`$key` = ?";
    }
    // Combine the SET clauses with commas
    $sql .= implode(', ', $updates);

    // Add a WHERE clause to specify which organization to update
    $sql .= " WHERE `username` =? AND `id` = ?";

    // Prepare and execute the query using queryPrepared
    $values = array_values($putData);
    $values[] = $putData['username']; // Add the name for the WHERE clause
    $values[] = $routeInfo[2]['id']; // Add the id for the WHERE clause

    $update_user = MYSQL::queryPrepared($sql, $values);

    if ($update_user->affected_rows === 0) {
        echo Output::error('Nothing updated');
    } else {
        echo Output::success('User updated');
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Secret header checks
    $checks->checkSecretHeader();
    // Now delete the user
    $deleteUser = MYSQL::queryPrepared('DELETE FROM `users` WHERE `id`=?', [$routeInfo[2]['id']]);
    if ($deleteUser->affected_rows === 0) {
        echo Output::error('User not found');
    } else {
        echo Output::success('User deleted');
    }
}
