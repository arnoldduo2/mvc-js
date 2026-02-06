<?php

declare(strict_types=1);

/**
 * Application Entry Point
 * 
 * This file serves as the main entry point for the MVC-JS application.
 * It loads the Composer autoloader, global constants, and bootstraps the application.
 */

// Load Composer's autoloader FIRST (this loads helpers.php with env() function)
require_once __DIR__ . '/vendor/autoload.php';

// Load global constants AFTER autoloader (so env() function is available)
require_once __DIR__ . '/src/config/constants.php';

// Your application bootstrap code goes here
// For example, you might load your application's bootstrap file:
require_once __DIR__ . '/src/boot/app.php';

// (new App())->run();