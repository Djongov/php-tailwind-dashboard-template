<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/app/' . $class . '.php';
});
