<?php

use Api\Output;
use Database\MYSQL;
return var_dump($_POST);
return var_dump($vars);

$users = MYSQL::query('SELECT * FROM users');

$usersArray = $users->fetch_all(MYSQLI_ASSOC);

echo Output::success($usersArray);
