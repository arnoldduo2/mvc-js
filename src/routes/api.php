<?php

declare(strict_types=1);

/**
 * API Routes
 * 
 * Define all API routes for the application here.
 * These routes return JSON responses.
 */

use App\App\Core\Router;

// Public API routes
Router::get('/api/health', function () {
   header('Content-Type: application/json');
   echo json_encode([
      'status' => 'ok',
      'app' => APP_NAME,
      'env' => APP_ENV,
      'timestamp' => time(),
   ]);
})->name('api.health');

// Protected API routes (require authentication)
Router::group(['prefix' => 'api', 'middleware' => 'auth'], function () {

   // User endpoints
   Router::get('/users', function () {
      header('Content-Type: application/json');
      echo json_encode([
         'data' => [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
         ],
         'total' => 2,
      ]);
   })->name('api.users.index');

   Router::get('/users/{id}', function ($id) {
      header('Content-Type: application/json');
      echo json_encode([
         'data' => [
            'id' => $id,
            'name' => 'User ' . $id,
            'email' => "user{$id}@example.com",
         ],
      ]);
   })->where('id', '[0-9]+')->name('api.users.show');

   // Create user (requires permission)
   Router::post('/users', function () {
      header('Content-Type: application/json');
      $data = json_decode(file_get_contents('php://input'), true);

      echo json_encode([
         'success' => true,
         'message' => 'User created successfully',
         'data' => $data,
      ]);
   })->middleware('permission:users.create')->name('api.users.store');

   // Update user (requires permission)
   Router::put('/users/{id}', function ($id) {
      header('Content-Type: application/json');
      $data = json_decode(file_get_contents('php://input'), true);

      echo json_encode([
         'success' => true,
         'message' => "User {$id} updated successfully",
         'data' => $data,
      ]);
   })->middleware('permission:users.update')->name('api.users.update');

   // Delete user (requires permission)
   Router::delete('/users/{id}', function ($id) {
      header('Content-Type: application/json');
      echo json_encode([
         'success' => true,
         'message' => "User {$id} deleted successfully",
      ]);
   })->middleware('permission:users.delete')->name('api.users.destroy');
});

// Admin API routes (require admin role)
Router::group(['prefix' => 'api/admin', 'middleware' => ['auth', 'role:admin']], function () {
   Router::get('/stats', function () {
      header('Content-Type: application/json');
      echo json_encode([
         'users' => 150,
         'posts' => 1250,
         'comments' => 3400,
      ]);
   })->name('api.admin.stats');
});
