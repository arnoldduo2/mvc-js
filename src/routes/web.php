<?php

declare(strict_types=1);

/**
 * Web Routes
 * 
 * Define all web routes for the application here.
 * These routes return HTML responses.
 */

use App\App\Core\Router;
use App\App\Controllers\HomeController;

// Default route
Router::get('/', [HomeController::class, 'index'])->name('home');
