<?php
spl_autoload_register(function ($class) {
    // Define the base directory for your classes (change this if necessary)
    $baseDir = dirname($_SERVER['DOCUMENT_ROOT']) . '/app/';

    // Convert the namespace to a path
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    // Construct the full path to the class file
    $filePath = $baseDir . $classPath . '.php';

    // Check if the file exists before including it
    if (file_exists($filePath)) {
        require_once $filePath;
    }
});
