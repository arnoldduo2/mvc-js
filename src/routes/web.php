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

// System Requirements Check
Router::get('/requirements', function () {
   $checker = new \App\App\Services\RequirementChecker();
   $results = $checker->check();
   $passes = $checker->passes();

   // Simple inline view for diagnostics
   echo "<!DOCTYPE html><html><head><title>System Requirements</title>";
   echo "<style>body{font-family:sans-serif;padding:2rem;max-width:800px;margin:0 auto;background:#f4f4f4}";
   echo ".card{background:white;padding:2rem;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1)}";
   echo "h1{margin-top:0}.pass{color:green}.fail{color:red}.item{display:flex;justify-content:space-between;border-bottom:1px solid #eee;padding:0.5rem 0}";
   echo ".message{font-size:0.9em;color:#666;margin-top:0.25rem}</style></head><body>";
   echo "<div class='card'><h1>System Requirements</h1>";

   // Overall Status
   echo "<h3>Overall Status: " . ($passes ? "<span class='pass'>PASS</span>" : "<span class='fail'>FAIL</span>") . "</h3>";

   // PHP
   $php = $results['php'];
   echo "<div class='item'><strong>PHP Version</strong><span class='" . ($php['pass'] ? 'pass' : 'fail') . "'>" . ($php['pass'] ? 'PASS' : 'FAIL') . "</span></div>";
   echo "<div class='message'>{$php['message']}</div>";

   echo "<h4>Extensions</h4>";
   foreach ($results['extensions'] as $ext) {
      echo "<div class='item'><span>{$ext['name']}</span><span class='" . ($ext['pass'] ? 'pass' : 'fail') . "'>" . ($ext['pass'] ? 'PASS' : 'FAIL') . "</span></div>";
   }

   echo "<h4>Functions</h4>";
   foreach ($results['functions'] as $func) {
      echo "<div class='item'><span>{$func['name']}</span><span class='" . ($func['pass'] ? 'pass' : 'fail') . "'>" . ($func['pass'] ? 'PASS' : 'FAIL') . "</span></div>";
   }

   echo "</div></body></html>";
});
