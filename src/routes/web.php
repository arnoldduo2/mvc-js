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
use App\App\Controllers\CacheController;
use App\App\Controllers\FormController;

// Public routes with SPA support
Router::get('/', [HomeController::class, 'index'])->name('home');
Router::get('/about', [HomeController::class, 'about'])->name('about');
Router::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

// Cache demo routes
Router::get('/cache', [CacheController::class, 'index'])->name('cache.demo');
Router::get('/cache/test', [CacheController::class, 'testBasic'])->name('cache.test');
Router::get('/cache/stats', [CacheController::class, 'stats'])->name('cache.stats');
Router::get('/cache/clear', [CacheController::class, 'clear'])->name('cache.clear');
Router::get('/cache/cleanup', [CacheController::class, 'cleanup'])->name('cache.cleanup');

// Form validation demo routes
Router::get('/forms', [FormController::class, 'index'])->name('forms.demo');
Router::post('/forms/submit', [FormController::class, 'submit'])->name('forms.submit');
Router::get('/forms/success', [FormController::class, 'success'])->name('forms.success');



// Login page (example)
Router::get('/login', function () {
   echo "<h1>Login</h1>";
   echo "<p>Login form would go here.</p>";
   echo "<p><a href='/'>Home</a></p>";
})->name('login');

// Admin routes (require authentication + role)
Router::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:admin']], function () {
   Router::get('/users', function () {
      echo "<h1>Admin - Users</h1>";
      echo "<p>User management page</p>";
   })->name('admin.users');

   Router::get('/settings', function () {
      echo "<h1>Admin - Settings</h1>";
      echo "<p>Settings page</p>";
   })->name('admin.settings');
});

// Test route for automatic asset injection
Router::get('/test/auto-assets', function () {
   return \App\App\Core\View::page('test.auto-assets', [], 'Asset Injection Test');
});

// Test route for Dependency Injection
Router::get('/test/di', [\App\App\Controllers\TestDIController::class, 'index']);
