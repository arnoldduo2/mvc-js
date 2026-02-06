<?php

declare(strict_types=1);

/**
 * Application Bootstrap File
 * 
 * This file initializes and runs the application.
 */

use App\App\Core\Application;

// Get the application instance and boot it
$app = Application::getInstance();

// Boot the application (loads system files, routes, etc.)
$app->boot();

// Run the application (handles the request)
$app->run();