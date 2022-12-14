<?php

declare(strict_types=1);

use data\failure\Failure;

const __debug__ = true;

// display errors
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

# throw all errors
error_reporting(E_ALL);
ini_set('display_errors', '1');
# set timezone
date_default_timezone_set('Europe/Berlin');
# throw error on warning
set_error_handler(/**
 * @throws ErrorException
 */ function (
  $errno,
  $errstr,
  $errfile,
  $errline
) {
  if (App::$logFile !== null) {
    App::logInfo("Error: $errstr in $errfile on line $errline");
  } else {
    echo "GOT ERROR, BUT CANT LOG; LOGFILE IS NULL";
  }
  throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});


// autoloader by namespace
spl_autoload_register(/**
 * @throws Exception
 */ function (
  $class
) {
  $class = str_replace("\\", "/", $class);
  $file = __DIR__ . "/$class.php";
  if (file_exists($file)) {
    include $file;
  } else {
    throw new Exception("File $file not found");
  }
});


session_start();

include "App.php";




